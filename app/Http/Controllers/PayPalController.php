<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PayPalController extends Controller
{
    public function createOrder($id, Request $request)
    {
        $payment = Payment::with('items', 'merchants')->findOrFail($id);
        $total = $payment->price;
        return response()->json([
            'id' => uniqid('paypal_order_')
        ]);
    }

    public function captureOrder($id, Request $request)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = 2;
        $payment->return_response = json_encode($request->all());
        $payment->save();
        return response()->json(['status' => 'success']);
    }

    public function paymentCancel($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = 1;
        $payment->return_response = 'Payment cancelled by user.';
        $payment->save();
        return redirect()->route('declined.payment', $payment->id);
    }

    public function paymentSuccess($id, Request $request)
    {
        $paymentData = Payment::findOrFail($id);
        $details = $request->all();

        if (!empty($details['id'])) {
            $paymentData->status = 2;
            $paymentData->return_response = json_encode($details);
            $paymentData->save();
            return response()->json(['status' => 'success']);
        }
        $paymentData->status = 1;
        $paymentData->return_response = 'Payment not completed.';
        $paymentData->save();
        return response()->json(['status' => 'error']);
    }

}
