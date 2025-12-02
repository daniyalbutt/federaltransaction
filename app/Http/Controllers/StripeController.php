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
        if ($data->status == 0) {
            try {
                \Stripe\Stripe::setApiKey($data->merchants->private_key);
                $get_customer = \Stripe\Customer::create([
                    'name' => $request->ssl_first_name . ' ' . $request->ssl_last_name,
                    'email' => $data->client->email,
                    'phone' => $data->client->brand_name,
                    'source' => $request->stripeToken,
                ]);
            
                $get_charge = \Stripe\Charge::create([
                    'customer' => $get_customer->id,
                    'amount' => $data->price * 100,
                    'currency' => 'usd',
                    'description' => $data->package,
                ]);
                $chargeJson = $get_charge->jsonSerialize();
                if ($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1) {
                    $data->update(['status'=> 2,'return_response'=>json_encode($chargeJson),'payment_data'=>$request->except(['amount','_token','id'])]);
                    return redirect()->route('success.payment', ['id' => $data->id]);
                }
            } catch (\Stripe\Exception\CardException $e) {
                $data->update(['square_response'=> json_encode($e->getError()), 'status'=>1,'return_response'=> $e->getError()->message,'payment_data'=>$request->except(['amount','_token','id'])]);
                return redirect()->route('declined.payment', ['id' => $data->id]);
            } catch (\Stripe\Exception\RateLimitException $e) {
                $data->update(['square_response'=> json_encode($e->getError()), 'status'=>1,'return_response'=> $e->getError()->message,'payment_data'=>$request->except(['amount','_token','id'])]);
                return redirect()->route('declined.payment', ['id' => $data->id]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $data->update(['square_response'=> json_encode($e->getError()), 'status'=>1,'return_response'=> $e->getError()->message,'payment_data'=>$request->except(['amount','_token','id'])]);
                return redirect()->route('declined.payment', ['id' => $data->id]);
            } catch (\Stripe\Exception\AuthenticationException $e) {
                $data->update(['square_response'=> json_encode($e->getError()), 'status'=>1,'return_response'=> $e->getError()->message,'payment_data'=>$request->except(['amount','_token','id'])]);
                return redirect()->route('declined.payment', ['id' => $data->id]);
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $data->update(['square_response'=> json_encode($e->getError()), 'status'=>1,'return_response'=> $e->getError()->message,'payment_data'=>$request->except(['amount','_token','id'])]);
                return redirect()->route('declined.payment', ['id' => $data->id]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $data->update(['square_response'=> json_encode($e->getError()), 'status'=>1,'return_response'=> $e->getError()->message,'payment_data'=>$request->except(['amount','_token','id'])]);
                return redirect()->route('declined.payment', ['id' => $data->id]);
            } catch (Exception $e) {
                $data->update(['square_response'=> json_encode($e->getError()), 'status'=>1,'return_response'=> $e->getMessage(),'payment_data'=>$request->except(['amount','_token','id'])]);
                return redirect()->route('declined.payment', ['id' => $data->id]);
            }
            Session::flash('success', 'Payment Successful !');
        }
    }
    
    public function successPayment($id){
        $data = Payment::find($id);
        $transaction_id = '';
        if($data->merchant == 0){
            if($data->status == 2){
                $transaction_id = json_decode($data->return_response)->id;
            }
        }
        return view('payment-success', compact('id', 'transaction_id', 'data'));
    }
    
    public function declinedPayment($id){
        $data = Payment::find($id);
        $transaction_id = '';
        return view('payment-declined', compact('id', 'transaction_id'));
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
        $input_request = $request->input();
        $payments_id = $request->id;
        $payments = Payment::find($payments_id);
        if ($payments->status == 0) {
            $name = $request->user_name;
            $email = $request->user_email;
            $city = $request->city;
            $country = $request->country;
            $set_state = $request->set_state;
            $address = $request->address;
            $zip = $request->cc_zip;
            
            $cardNumber = preg_replace('/\s+/', '', $request->input('cc_number'));
            
            $format_exp = $request->exp_month;
            $format_year = '20' . $request->exp_year;
            
            $expirationDateNew = $format_year . '-' . $format_exp;
            
            $cvv = $request->input('cc_cvc');
            
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($payments->merchants->public_key);
            $merchantAuthentication->setTransactionKey($payments->merchants->private_key);
            
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($cardNumber);
            $creditCard->setExpirationDate($expirationDateNew);
            $creditCard->setCardCode($cvv);
    
            $payment = new AnetAPI\PaymentType();
            $payment->setCreditCard($creditCard);
            
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($payments->id);
            $order->setDescription($payments->package);
            
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($name);
            $customerAddress->setAddress($address);
            $customerAddress->setZip($zip);
            $customerAddress->setCity($city);
            $customerAddress->setState($set_state);
            $customerAddress->setZip($zip);
            $customerAddress->setCountry($country);
            
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setId($payments->client_id);
            $customerData->setemail($email);
    
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($payments->price);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($payment);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setCustomer($customerData);
    
            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId("ref" . time());
            $request->setTransactionRequest($transactionRequestType);
    
            $controller = new AnetController\CreateTransactionController($request);
            if($payments->merchants->sandbox == 0){
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            }else{
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            }
            $update_payments = Payment::find($payments->id);
            $update_payments->payment_data = $input_request;
            if ($response != null) {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getResponseCode() == "1") {
                    $update_payments->status = 2;
                    $update_payments->return_response = 'Payment Successfully - ' . $tresponse->getMessages()[0]->getDescription();
                    $responseArray = json_decode(json_encode($tresponse), true);
                    $responseArray['card_brand'] = $tresponse->getAccountType();
                    $update_payments->authorize_response = $responseArray;
                    $update_payments->save();
                    return redirect()->route('success.payment', ['id' => $payments->id]);
                } else {
                    $update_payments->status = 1;
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $update_payments->return_response = $tresponse->getErrors()[0]->getErrorText();
                        $update_payments->authorize_response = json_encode($tresponse);
                    } else {
                        $update_payments->return_response = $response->getMessages()->getMessage()[0]->getText();
                        $update_payments->authorize_response = json_encode($response);
                    }
                    $update_payments->save();
                    return redirect()->route('declined.payment', ['id' => $payments->id]);
                }
            } else {
                $update_payments->status = 1;
                $update_payments->return_response = $response->getMessages()->getMessage()[0]->getText();
                $update_payments->authorize_response = json_encode($response);
                $update_payments->save();
                return redirect()->route('declined.payment', ['id' => $payments->id]);
            }
            
            
        }else{
            return response()->json([
                'status' => 'declined',
                'data' => 'Already Used'
            ], 200);
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
