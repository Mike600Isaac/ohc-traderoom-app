<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - OHC Traderoom</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ohc-auth-page ohc-auth-register-page">
    <main class="ohc-auth-shell">
        <section class="ohc-auth-panel">
            <a href="/" class="ohc-brand" aria-label="OHC Trade Room homepage">
                <x-brand-logo variant="light" />
            </a>

            <div class="ohc-auth-copy">
                <p class="ohc-eyebrow">Investor-grade onboarding</p>
                <h1 class="ohc-register-title"><span class="ohc-title-line">Create Traderoom account.</span></h1>
                <p>Your account will control course access, live sessions, subscriptions, and future investor tools.</p>
            </div>

            @include('components.live-market-tape')

            <ul class="ohc-security-list">
                <li>Secure account foundation</li>
                <li>Backend-verified payments ready</li>
                <li>Private course access ready</li>
            </ul>
        </section>

        <section class="ohc-auth-card ohc-register-card">
            <p class="ohc-eyebrow">Sign up</p>
            <h2 class="ohc-register-card-title"><span>Start with OHC</span><span>Traderoom</span></h2>
            <p class="ohc-muted">Create your secure profile to access courses, live sessions, and member tools.</p>
            <div class="ohc-guided-cursor ohc-guided-cursor--register" aria-hidden="true">
                <span></span>
            </div>

            <form method="POST" action="{{ route('register') }}" class="ohc-auth-form">
                @csrf

                <div class="ohc-field-grid">
                    <label>
                        <span>First name</span>
                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="given-name" placeholder="First name">
                        <x-input-error :messages="$errors->get('first_name')" class="ohc-error" />
                    </label>

                    <label>
                        <span>Last name</span>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Last name">
                        <x-input-error :messages="$errors->get('last_name')" class="ohc-error" />
                    </label>
                </div>

                <label>
                    <span>Email address</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com">
                    <x-input-error :messages="$errors->get('email')" class="ohc-error" />
                </label>

                <label>
                    <span>Password</span>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 12 characters">
                    <small>Use uppercase, lowercase, number, and symbol.</small>
                    <x-input-error :messages="$errors->get('password')" class="ohc-error" />
                </label>

                <label>
                    <span>Confirm password</span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="ohc-error" />
                </label>

                <button type="submit" class="ohc-auth-submit" aria-label="Create account">
                    Create account <span aria-hidden="true">→</span>
                </button>
            </form>

            <p class="ohc-switch">Already registered? <a href="{{ route('login') }}">Log in</a></p>
        </section>
    </main>
</body>
</html>
