<?php
// app/Mail/VerifyEmail.php
// app/Mail/VerifyEmail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationToken;

    public function __construct(User $user, $verificationToken)
    {
        $this->user = $user;
        $this->verificationToken = $verificationToken; // Store the token as a string
    }

    public function build()
    {
        return $this->view('emails.verify_email')
            ->subject('Verify Your Email')
            ->with([
                'user' => $this->user, // Pass the User object to the view
                'verificationToken' => $this->verificationToken, // Pass the token to the view
            ]);
    }
}
