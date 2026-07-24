<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - OHC Traderoom</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ohc-auth-page">
    <main class="ohc-auth-shell">
        <section class="ohc-auth-panel">
            <a href="/" class="ohc-brand">
                <span class="ohc-brand-mark">OHC</span>
                <span class="ohc-brand-divider"></span>
                <span>Trade Room</span>
            </a>

            <div class="ohc-auth-copy">
                <p class="ohc-eyebrow">Account recovery</p>
                <h1>Reset access to Traderoom password.</h1>
                <p>Enter your email and we will send a protected reset link so you can regain access to your traderoom account.</p>
            </div>

            <div class="ohc-fx-ticker" aria-label="Animated currency rates">
                <div class="ohc-fx-track">
                    <span>EUR/USD <strong>1.0862</strong></span>
                    <span>GBP/USD <strong>1.2764</strong></span>
                    <span>USD/JPY <strong>156.21</strong></span>
                    <span>XAU/USD <strong>2,417.50</strong></span>
                    <span>BTC/USD <strong>67,820</strong></span>
                    <span>EUR/USD <strong>1.0862</strong></span>
                    <span>GBP/USD <strong>1.2764</strong></span>
                    <span>USD/JPY <strong>156.21</strong></span>
                </div>
            </div>
        </section>

        <section class="ohc-auth-card">
            <p class="ohc-eyebrow">Forgot password</p>
            <h2>Recover your account</h2>
            <p class="ohc-muted">We will email a reset link if the address belongs to an OHC Traderoom account.</p>

            <x-auth-session-status class="ohc-status" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="ohc-auth-form">
                @csrf

                <label>
                    <span>Email address</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
                    <x-input-error :messages="$errors->get('email')" class="ohc-error" />
                </label>

                <button type="submit" class="ohc-auth-submit" aria-label="Send password reset link">
                    Send reset link <span aria-hidden="true">→</span>
                </button>
            </form>

            <p class="ohc-switch">Remembered it? <a href="{{ route('login') }}">Return to login</a></p>
        </section>
    </main>
</body>
</html>
