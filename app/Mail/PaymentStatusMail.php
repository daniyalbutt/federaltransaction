<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    /**
     * Create a new message instance.
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->payment->status == 2 ? 'Invoice Paid Successfully' : 'Payment Declined';
        return $this->subject($subject)
                    ->view('emails.payment-status');
    }
}
