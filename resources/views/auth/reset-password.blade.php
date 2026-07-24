<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create New Password - OHC Traderoom</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ohc-auth-page ohc-auth-new-password-page">
    <main class="ohc-auth-shell">
        <section class="ohc-auth-panel">
            <a href="/" class="ohc-brand">
                <span class="ohc-brand-mark">OHC</span>
                <span class="ohc-brand-divider"></span>
                <span>Trade Room</span>
            </a>

            <div class="ohc-auth-copy">
                <p class="ohc-eyebrow">Secure password reset</p>
                <h1>Set a new Traderoom password.</h1>
                <p>Choose a strong password to protect course access, live sessions, subscriptions, and member tools.</p>
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
            <p class="ohc-eyebrow">Reset password</p>
            <h2>Protect your access</h2>
            <p class="ohc-muted">Enter your email and create a new secure password for your Traderoom account.</p>
            <div class="ohc-guided-cursor ohc-guided-cursor--new-password" aria-hidden="true">
                <span></span>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="ohc-auth-form">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <label>
                    <span>Email address</span>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="name@example.com">
                    <x-input-error :messages="$errors->get('email')" class="ohc-error" />
                </label>

                <label>
                    <span>New password</span>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 12 characters">
                    <x-input-error :messages="$errors->get('password')" class="ohc-error" />
                </label>

                <label>
                    <span>Confirm password</span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="ohc-error" />
                </label>

                <button type="submit" class="ohc-auth-submit" aria-label="Reset password">
                    Reset password <span aria-hidden="true">→</span>
                </button>
            </form>

            <p class="ohc-switch">Remembered it? <a href="{{ route('login') }}">Return to login</a></p>
        </section>
    </main>
</body>
</html>
