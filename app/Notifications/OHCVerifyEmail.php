<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class OHCVerifyEmail extends VerifyEmail
{
    /**
     * Build the OHC-branded verification message while retaining Laravel's
     * temporary, signed verification URL.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $firstName = trim((string) ($notifiable->first_name ?? ''));
        $greeting = $firstName !== '' ? "Hello {$firstName}," : 'Hello,';
        $expiresIn = (int) config('auth.verification.expire', 60);

        return (new MailMessage)
            ->subject('Verify your OHC Trade Room email')
            ->greeting($greeting)
            ->line('Welcome to OHC Trade Room. Confirm this email address to protect your account and unlock the member workspace.')
            ->action('Verify email address', $verificationUrl)
            ->line("For your security, this link expires in {$expiresIn} minutes.")
            ->line('If you did not create this account, you can safely ignore this email.')
            ->salutation("OHC Trade Room\nInstitutional intelligence. Systematic execution. Generational wealth.");
    }
}
