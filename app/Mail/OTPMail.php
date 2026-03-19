<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function __construct(public string $otp)
    {
        $this->onQueue("emails");
    }

    public function build()
    {
        return $this->subject('Your OTP Code')->view('emails.otp', [
            'otp' => $this->otp,
        ]);

    }

    public function failed(\Throwable $throwable): void
    {
    }
}