<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - OHC Trade Room</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ohc-auth-page ohc-auth-verify-page">
    <main class="ohc-auth-shell">
        <section class="ohc-auth-panel">
            <a href="{{ route('home') }}" class="ohc-brand" aria-label="OHC Trade Room homepage">
                <x-brand-logo variant="light" />
            </a>

            <div class="ohc-auth-copy">
                <p class="ohc-eyebrow">Account protection</p>
                <h1><span class="ohc-title-line">Confirm your email.</span></h1>
                <p>One final security step protects your identity and ensures that account notices reach the right inbox.</p>
            </div>

            <ul class="ohc-security-list" aria-label="Why email verification matters">
                <li>Protects course and subscription access</li>
                <li>Secures password recovery</li>
                <li>Confirms important member communications</li>
            </ul>
        </section>

        <section class="ohc-auth-card ohc-verification-card">
            <div class="ohc-verification-icon" aria-hidden="true">&#9993;</div>
            <p class="ohc-eyebrow">Check your inbox</p>
            <h2>Verify your email address</h2>
            <p class="ohc-muted">We sent a secure verification link to:</p>
            <p class="ohc-verification-email">{{ auth()->user()->email }}</p>
            <p class="ohc-verification-help">Open the message and select <strong>Verify email address</strong>. The link expires in {{ config('auth.verification.expire', 60) }} minutes.</p>

            @if (in_array(session('status'), ['verification-link-sent', 'verification-email-sent'], true))
                <div class="ohc-verification-success" role="status">
                    A fresh verification link has been sent. Please also check your spam or promotions folder.
                </div>
            @endif

            <div class="ohc-verification-actions">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="ohc-auth-submit">Resend verification email</button>
                </form>

                <a class="ohc-auth-secondary" href="{{ route('profile.edit') }}">Change email address</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="ohc-verification-logout">
                @csrf
                <button type="submit">Log out and use another account</button>
            </form>
        </section>
    </main>
</body>
</html>
