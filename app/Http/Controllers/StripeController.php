<?php

namespace App\Http\Controllers;

use Session;
use Stripe;
use Exception;
use App\Models\Payment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function stripe()
    {
        return view('stripe');
    }

    public function stripePost(Request $request)
    {
        $data = Payment::find($request->input('id'));
        if (!$data || $data->status != 0) {
            abort(404, 'Payment not found or already processed.');
        }
        try {
            \Stripe\Stripe::setApiKey($data->merchants->private_key);
            $name = trim($request->input('ssl_first_name', $data->client->name) . ' ' . $request->input('ssl_last_name', ''));
            $get_customer = \Stripe\Customer::create([
                'name' => $name,
                'email' => $data->client->email,
                'phone' => $data->client->phone ?? '',
                'source' => $request->stripeToken,
            ]);
        
            $get_charge = \Stripe\Charge::create([
                'customer' => $get_customer->id,
                'amount' => (int) round($data->price * 100),
                'currency' => 'usd',
                'description' => $data->package,
            ]);
            $chargeJson = $get_charge->jsonSerialize();
            if ($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1) {
                $data->update([
                    'status' => 2,
                    'return_response' => json_encode($chargeJson),
                    'payment_data' => $request->except(['amount','_token','id','stripeToken'])
                ]);
                return redirect()->route('success.payment', ['id' => $data->id]);
            }
            $data->update([
                'status' => 1,
                'return_response' => json_encode($chargeJson),
                'payment_data' => $request->except(['amount','_token','id','stripeToken'])
            ]);
            return redirect()->route('declined.payment', ['id' => $data->id]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $data->update([
                'status' => 1,
                'return_response' => $e->getError()->message ?? $e->getMessage(),
                'square_response' => json_encode($e->getError()),
                'payment_data' => $request->except(['amount','_token','id','stripeToken'])
            ]);
            return redirect()->route('declined.payment', ['id' => $data->id]);

        } catch (\Exception $e) {
            $data->update([
                'status' => 1,
                'return_response' => $e->getMessage(),
                'square_response' => null,
                'payment_data' => $request->except(['amount','_token','id','stripeToken'])
            ]);
            return redirect()->route('declined.payment', ['id' => $data->id]);
        }
    }
    
    public function successPayment($id){
        $data = Payment::find($id);
        $transaction_id = '';
        $transactionMessage = '';
        if($data->merchant == 0){
            if($data->status == 2){
                $transaction_id = json_decode($data->return_response)->id;
            }
        }
        if ($data->status == 2 && $data->merchants->merchant == 4) {
            $transactionMessage = $data->return_response ?? 'Your will be redirected to the homepage shortly or click here to return to homepage.';
        }
        return view('payment-success', compact('id', 'transaction_id', 'data', 'transactionMessage'));
    }
    
    public function declinedPayment($id){
        $data = Payment::find($id);
        $transactionMessage = '';
        if ($data->status == 1 && $data->merchants->merchant == 0) {
            $transactionMessage = 'Reason: ' . $data->return_response ?? 'Your payment was declined.';
        }
        if ($data->status == 1 && $data->merchants->merchant == 4) {
            $transactionMessage = $data->return_response ?? 'Your payment was declined.';
        }
        $transaction_id = '';
        return view('payment-declined', compact('id', 'transaction_id', 'transactionMessage'));
    }
    
    public function processPayment(Request $request)
    {
        $payments_id = $request->id;
        $payments = Payment::find($payments_id);
        if ($payments->status == 0) {
            $name = $request->ssl_first_name . ' ' . $request->ssl_last_name;
            $email = $request->ssl_email;
            $address = $request->ssl_avs_address;
            $accessToken = env('SQUARE_ACCESS_TOKEN');
            $this->locationId = env('SQUARE_LOCATION_ID');
            $defaultApiConfig = new \SquareConnect\Configuration();
            if (env('SQUARE_ENVIRONMENT') == 'sandbox') {
                $defaultApiConfig->setHost("https://connect.squareupsandbox.com");
            } else {
                $defaultApiConfig->setHost("https://connect.squareup.com");
            }
            $defaultApiConfig->setAccessToken($accessToken);
            $this->defaultApiClient = new \SquareConnect\ApiClient($defaultApiConfig);
            $cardNonce = $request->token;
            $customersApi = new \SquareConnect\Api\CustomersApi($this->defaultApiClient);
            $customerId = $this->addCustomer($name, $email, $address);
            $body = new \SquareConnect\Model\CreateCustomerCardRequest();
            $body->setCardNonce($cardNonce);
            try {
                $result = $customersApi->createCustomerCard($customerId, $body);
                $card_id = $result->getCard()->getId();
                $card_brand = $result->getCard()->getCardBrand();
                $card_last_four = $result->getCard()->getLast4();
                $card_exp_month = $result->getCard()->getExpMonth();
                $card_exp_year = $result->getCard()->getExpYear();
                $data_return = $this->charge($customerId, $card_id, $payments->price);
                $update_payments = Payment::find($payments->id);
                if ($data_return[0] != 0) {
                    $converted = json_encode(serialize($data_return[1]));
                    $update_payments->square_response = $converted;
                    if ($data_return[1]->getPayment()->getStatus() == 'COMPLETED') {
                        $update_payments->status = 2;
                        $update_payments->return_response = 'Payment Successfully - ' . $data_return[1]->getPayment()->getStatus();
                        $update_payments->save();
                        return redirect()->route('success.payment', ['id' => $payments->id]);
                    } else {
                        $update_payments->status = 1;
                        $update_payments->return_response = 'Card Declined';
                        $update_payments->save();
                        return redirect()->route('declined.payment', ['id' => $payments->id]);
                    }
                } else {
                    $response = $data_return[1];
                    $error_string = "";
                    foreach ($response->errors as &$error) {
                        $error_string .= $error->detail . "<br>";
                    }
                    $update_payments = Payment::find($payments->id);
                    $update_payments->status = 1;
                    $update_payments->return_response = $error_string;
                    $update_payments->save();
                    return redirect()->route('declined.payment', ['id' => $payments->id]);
                }
                $update_payments->save();

                return response()->json([
                    'status' => 'declined',
                    'data' => 'Checker'
                ], 200);
            } catch (Exception $e) {
                $response = $e->getResponseBody();
                $error_string = "";
                foreach ($response->errors as &$error) {
                    $error_string .= $error->detail . "<br>";
                }
                $update_payments = Payment::find($payments->id);
                $update_payments->status = 1;
                $update_payments->return_response = $error_string;
                $update_payments->save();
                return redirect()->route('declined.payment', ['id' => $payments->id]);
                return response()->json([
                    'status' => 'declined',
                    'data' => $error_string
                ], 200);
            } catch (\SquareConnect\ApiException $e) {
                $response = $e->getResponseBody();
                $error_string = "";
                foreach ($response->errors as &$error) {
                    $error_string .= $error->detail . "<br>";
                }
                $update_payments = Payment::find($payments->id);
                $update_payments->status = 1;
                $update_payments->return_response = $error_string;
                $update_payments->save();
                return redirect()->route('declined.payment', ['id' => $payments->id]);
                return response()->json([
                    'status' => 'declined',
                    'data' => $error_string
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'declined',
                'data' => 'Already Used'
            ], 200);
        }
    }


    public function addCustomer($customer_name, $customer_email, $customer_address)
    {

        $name = $customer_name;
        $email = $customer_email;

        $customer = new \SquareConnect\Model\CreateCustomerRequest();
        $customer->setGivenName($name);
        $customer->setEmailAddress($email);

        $customer_address = new \SquareConnect\Model\Address();
        $customer_address->setAddressLine1($customer_address);

        $customer->setAddress = $customer_address;


        $customersApi = new \SquareConnect\Api\CustomersApi($this->defaultApiClient);

        try {
            $result = $customersApi->createCustomer($customer);
            $id = $result->getCustomer()->getId();
            return $id;
        } catch (Exception $e) {
            dump($e->getMessage());
            return "";
        }
        return "";
    }

    public function charge($customerId, $cardId, $price)
    {

        $payments_api = new \SquareConnect\Api\PaymentsApi($this->defaultApiClient);
        $payment_body = new \SquareConnect\Model\CreatePaymentRequest();

        $amountMoney = new \SquareConnect\Model\Money();

        # Monetary amounts are specified in the smallest unit of the applicable currency.
        # This amount is in cents. It's also hard-coded for $1.00, which isn't very useful.
        $amountMoney->setAmount($price * 100);
        $amountMoney->setCurrency("USD");
        $payment_body->setCustomerId($customerId);
        $payment_body->setSourceId($cardId);
        $payment_body->setAmountMoney($amountMoney);
        $payment_body->setLocationId($this->locationId);

        # Every payment you process with the SDK must have a unique idempotency key.
        # If you're unsure whether a particular payment succeeded, you can reattempt
        # it with the same idempotency key without worrying about double charging
        # the buyer.
        $payment_body->setIdempotencyKey(uniqid());

        try {
            $result = $payments_api->createPayment($payment_body);
            $transaction_id = $result->getPayment()->getId();
            return [$transaction_id, $result];
        } catch (\SquareConnect\ApiException $e) {
            return [0, $e->getResponseBody()];
        }
    }
    
    public function paymentAuthorize(Request $request){
        $payments = Payment::find($request->id);
        if (!$payments || $payments->status != 0) {
            return response()->json([
                'status' => 'declined',
                'message' => 'Payment already processed or invalid payment ID.'
            ], 400);
        }
        $merchant = $payments->merchants;
        if (!$merchant || empty($merchant->public_key) || empty($merchant->private_key)) {
            return redirect()->back()->withErrors(['merchant' => 'Merchant keys are not properly configured.']);
        }
        $expMonth = str_pad($request->exp_month, 2, '0', STR_PAD_LEFT);
        $expYear = '20' . str_pad($request->exp_year, 2, '0', STR_PAD_LEFT);
        $expirationDate = $expYear . '-' . $expMonth;
        if ($payments->price <= 0) {
            return redirect()->back()->withErrors(['amount' => 'Payment amount must be greater than 0']);
        }
        try {
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($merchant->public_key);
            $merchantAuthentication->setTransactionKey($merchant->private_key);
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber(preg_replace('/\s+/', '', $request->input('cc_number')));
            $creditCard->setExpirationDate($expirationDate);
            $creditCard->setCardCode($request->input('cc_cvc'));
            $paymentType = new AnetAPI\PaymentType();
            $paymentType->setCreditCard($creditCard);
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($payments->id);
            $order->setDescription($payments->package);
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($request->user_name);
            $customerAddress->setAddress($request->address);
            $customerAddress->setCity($request->city);
            $customerAddress->setState($request->state ?? '');
            $customerAddress->setZip($request->cc_zip);
            $customerAddress->setCountry($request->country);
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setId($payments->client_id);
            $customerData->setEmail($request->user_email);
            $transactionRequest = new AnetAPI\TransactionRequestType();
            $transactionRequest->setTransactionType("authCaptureTransaction");
            $transactionRequest->setAmount($payments->price);
            $transactionRequest->setOrder($order);
            $transactionRequest->setPayment($paymentType);
            $transactionRequest->setBillTo($customerAddress);
            $transactionRequest->setCustomer($customerData);
            $transactionRequestObj = new AnetAPI\CreateTransactionRequest();
            $transactionRequestObj->setMerchantAuthentication($merchantAuthentication);
            $transactionRequestObj->setRefId("ref" . time());
            $transactionRequestObj->setTransactionRequest($transactionRequest);
            $controller = new AnetController\CreateTransactionController($transactionRequestObj);
            $environment = $merchant->sandbox == 0
                ? \net\authorize\api\constants\ANetEnvironment::PRODUCTION
                : \net\authorize\api\constants\ANetEnvironment::SANDBOX;
            $response = $controller->executeWithApiResponse($environment);
            $updateData = ['payment_data' => $request->all()];
            if ($response !== null) {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse !== null && $tresponse->getResponseCode() == "1") {
                    $responseArray = json_decode(json_encode($tresponse), true);
                    $responseArray['card_brand'] = $tresponse->getAccountType();
                    $updateData['status'] = 2;
                    $updateData['return_response'] = 'Payment Successfully - ' . $tresponse->getMessages()[0]->getDescription();
                    $updateData['authorize_response'] = $responseArray;
                    $payments->update($updateData);
                    return redirect()->route('success.payment', ['id' => $payments->id]);
                } else {
                    $updateData['status'] = 1;
                    if ($tresponse !== null && $tresponse->getErrors() !== null) {
                        $updateData['return_response'] = $tresponse->getErrors()[0]->getErrorText();
                        $updateData['authorize_response'] = json_encode($tresponse);
                    } else {
                        $updateData['return_response'] = $response->getMessages()->getMessage()[0]->getText() ?? 'Transaction Declined';
                        $updateData['authorize_response'] = json_encode($response);
                    }
                    $payments->update($updateData);
                    return redirect()->route('declined.payment', ['id' => $payments->id]);
                }
            } else {
                $updateData['status'] = 1;
                $updateData['return_response'] = 'No response from Authorize.Net';
                $updateData['authorize_response'] = json_encode($response);
                $payments->update($updateData);
                return redirect()->route('declined.payment', ['id' => $payments->id]);
            }
        } catch (\Exception $e) {
            $payments->update([
                'status' => 1,
                'return_response' => $e->getMessage(),
                'authorize_response' => json_encode($e),
                'payment_data' => $request->all()
            ]);
            return redirect()->route('declined.payment', ['id' => $payments->id]);
        }
    }
    
    function chargeCreditCard($amount)
    {
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName("366kUQfgTZs");
        $merchantAuthentication->setTransactionKey("345X66RzPPNr3vsu");
       
        // Set the transaction's refId
        $refId = 'ref' . time();
     
        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber("4111111111111111");
        $creditCard->setExpirationDate("2038-12");
        $creditCard->setCardCode("123");
     
        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);
     
        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber("10101");
        $order->setDescription("Golf Shirts");
     
        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName("Ellen");
        $customerAddress->setLastName("Johnson");
        $customerAddress->setCompany("Souveniropolis");
        $customerAddress->setAddress("14 Main Street");
        $customerAddress->setCity("Pecan Springs");
        $customerAddress->setState("TX");
        $customerAddress->setZip("44628");
        $customerAddress->setCountry("USA");
     
        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId("99999456654");
        $customerData->setEmail("EllenJohnson@example.com");
     
        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");
     
        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType();
        $merchantDefinedField1->setName("customerLoyaltyNum");
        $merchantDefinedField1->setValue("1128836273");
     
        $merchantDefinedField2 = new AnetAPI\UserFieldType();
        $merchantDefinedField2->setName("favoriteColor");
        $merchantDefinedField2->setValue("blue");
     
        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);
     
        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
     
        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
       
     
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
           
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
                } else {
                    echo "Transaction Failed \n";
                    if ($tresponse->getErrors() != null) {
                        echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();
           
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                } else {
                    echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                }
            }
        } else {
            echo  "No response returned \n";
        }
     
        return $response;
    }
}
