<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Illuminate\Support\Facades\Schedule::command('admin:publish-scheduled')->everyMinute()->withoutOverlapping();

// <ohc-email-verification-command>
Artisan::command('ohc:verification:send {email}', function () {
    $email = mb_strtolower(trim((string) $this->argument('email')));
    $user = \App\Models\User::query()->where('email', $email)->first();

    if (! $user) {
        $this->error("No account was found for {$email}.");
        return 1;
    }

    if ($user->hasVerifiedEmail()) {
        $this->warn("{$email} is already verified.");
        return 0;
    }

    $user->sendEmailVerificationNotification();
    $this->info("Verification email sent to {$email} using the configured mailer.");
    return 0;
})->purpose('Send a verification email to an existing unverified account');
// </ohc-email-verification-command>
