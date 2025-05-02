<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    /**
     * Create a new message instance.
     *
     * @param  Payment  $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.payment_success')
                    ->subject('Payment Successful')
                    ->with([
                        'amount' => $this->payment->amount,
                        'payment_method' => $this->payment->payment_method,
                        'status' => $this->payment->status,
                        'transaction_id' => $this->payment->transaction_id,
                    ]);
    }
}
