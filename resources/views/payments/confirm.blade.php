
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirm Subscription - OHC Traderoom</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}" />
    <style>
      body {
        min-height: 100vh;
        margin: 0;
        background: linear-gradient(135deg, #08111f 0%, #10203d 52%, #0f766e 130%);
        color: #10203d;
        display: grid;
        place-items: center;
        padding: 32px;
      }

      .checkout-shell {
        width: min(920px, 100%);
        display: grid;
        grid-template-columns: 0.9fr 1.1fr;
        background: #ffffff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 30px 90px rgba(0, 0, 0, 0.28);
      }

      .checkout-brand {
        background: #10203d;
        color: #ffffff;
        padding: 42px;
      }

      .checkout-brand img {
        width: 160px;
        height: auto;
        margin-bottom: 42px;
      }

      .checkout-brand small {
        display: block;
        color: #5eead4;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        margin-bottom: 14px;
      }

      .checkout-brand h1 {
        margin: 0;
        font-size: clamp(34px, 5vw, 58px);
        line-height: 0.98;
        letter-spacing: 0;
      }

      .checkout-card {
        padding: 42px;
        display: flex;
        flex-direction: column;
        gap: 22px;
      }

      .checkout-card h2 {
        margin: 0;
        color: #10203d;
        font-size: 34px;
        line-height: 1.05;
      }

      .checkout-card p {
        color: #53627d;
        line-height: 1.7;
        margin: 0;
      }

      .checkout-summary {
        border: 1px solid #d9e3ee;
        border-radius: 14px;
        padding: 22px;
        background: #f8fafc;
      }

      .checkout-summary span {
        display: block;
        color: #2394a0;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        margin-bottom: 8px;
      }

      .checkout-price {
        font-size: 42px;
        font-weight: 900;
        color: #273a68;
        margin-top: 14px;
      }

      .checkout-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
      }

      .checkout-actions .btn {
        border: 0;
        cursor: pointer;
      }

      .checkout-note {
        font-size: 13px;
        color: #64748b;
      }

      .checkout-alert {
        border-radius: 12px;
        padding: 14px 16px;
        background: #fef2f2;
        color: #991b1b;
        font-weight: 700;
      }

      @media (max-width: 820px) {
        .checkout-shell {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    @php
      $isFreeCourse = (int) $course['amount'] <= 0;
    @endphp
    <main class="checkout-shell">
      <section class="checkout-brand">
        <img src="/images/logo-dark.png" alt="OHC Trade Room" />
        <small>{{ $isFreeCourse ? 'Course access' : 'Secure checkout' }}</small>
        <h1>{{ $isFreeCourse ? 'Activate your Traderoom course.' : 'Confirm your Traderoom access.' }}</h1>
      </section>

      <section class="checkout-card">
        @if (session('payment_error'))
          <div class="checkout-alert">{{ session('payment_error') }}</div>
        @endif

        <div>
          <h2>{{ $course['name'] }}</h2>
          <p>You are signed in as {{ auth()->user()->email }}. Payment will be linked to this OHC Traderoom account.</p>
        </div>

        <div class="checkout-summary">
          <span>{{ $course['offer_type'] }} access</span>
          <strong>{{ $course['name'] }}</strong>
          <div class="checkout-price">{{ $isFreeCourse ? 'Free' : $course['currency'] . ' ' . number_format($course['amount'] / 100) }}</div>
        </div>

        <form method="POST" action="{{ route('subscribe.start', $course['id']) }}" class="checkout-actions">
          @csrf
          <button type="submit" class="btn btn--teal">{{ $isFreeCourse ? 'Activate free course ->' : 'Proceed to secure payment ->' }}</button>
          <a href="/" class="btn btn--outline">Back to homepage</a>
        </form>

        @if ($isFreeCourse)
          <p class="checkout-note">This free course will be activated directly on your OHC Traderoom account. You must be signed in so access can be linked correctly.</p>
        @else
          <p class="checkout-note">After payment, OHC Traderoom will verify the transaction directly with Paystack before granting course access.</p>
        @endif
      </section>
    </main>
  </body>
</html>
