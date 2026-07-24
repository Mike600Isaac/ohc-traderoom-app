<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - OHC Traderoom</title>
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
                <p class="ohc-eyebrow">Secure member access</p>
                <h1><span class="ohc-title-line">Welcome to Traderoom.</span></h1>
                <p>Access courses, live sessions, investor resources, and protected member tools from one OHC workspace.</p>
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

            <div class="ohc-signal-card">
                <div>
                    <span>Risk Desk</span>
                    <strong>Protected</strong>
                </div>
                <div class="ohc-bars">
                    <i style="height: 36%"></i>
                    <i style="height: 62%"></i>
                    <i style="height: 48%"></i>
                    <i style="height: 84%"></i>
                    <i style="height: 58%"></i>
                    <i style="height: 72%"></i>
                </div>
            </div>
        </section>

        <section class="ohc-auth-card">
            <p class="ohc-eyebrow">Sign in</p>
            <h2>Continue securely</h2>
            <p class="ohc-muted">Use the account created directly with OHC Traderoom.</p>

            <x-auth-session-status class="ohc-status" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="ohc-auth-form">
                @csrf

                <label>
                    <span>Email address</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
                    <x-input-error :messages="$errors->get('email')" class="ohc-error" />
                </label>

                <label>
                    <span>Password</span>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                    <x-input-error :messages="$errors->get('password')" class="ohc-error" />
                </label>

                <div class="ohc-auth-row">
                    <label class="ohc-check" for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Remember this device</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="ohc-auth-submit" aria-label="Log in">
                    Log in <span aria-hidden="true">→</span>
                </button>
            </form>

            <p class="ohc-switch">New to OHC Traderoom? <a href="{{ route('register') }}">Create a secure account</a></p>
        </section>
    </main>
</body>
</html>
