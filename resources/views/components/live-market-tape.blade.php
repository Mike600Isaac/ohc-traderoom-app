<div class="ohc-live-market-tape" role="region" aria-label="Current market data">
  <style>
    .ohc-live-market-tape {
      width: 100%;
      min-width: 0;
      min-height: 0;
      overflow: hidden;
      margin: 0;
      padding: 0 !important;
      background: #0f1e3a;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      line-height: normal;
      box-sizing: border-box;
    }
    .ohc-live-market-tape .tradingview-widget-container,
    .ohc-live-market-tape .tradingview-widget-container__widget {
      width: 100%;
      min-width: 0;
      margin: 0;
      padding: 0;
    }
    .ohc-live-market-tape iframe {
      display: block;
      max-width: 100%;
      margin: 0;
    }
    .ohc-live-market-tape__loading {
      min-height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #cbd5e1;
      font: 600 12px/1.4 system-ui, sans-serif;
      letter-spacing: 0.04em;
    }
    .ohc-live-market-tape__note {
      margin: 0;
      padding: 3px 12px 4px;
      color: #94a3b8;
      background: #0f1e3a;
      font: 500 9px/1.25 system-ui, sans-serif;
      text-align: right;
      white-space: normal;
      box-sizing: border-box;
    }
    .ohc-live-market-tape__note a {
      color: #cbd5e1;
      text-decoration: underline;
      text-underline-offset: 2px;
    }
    @media (max-width: 640px) {
      .ohc-live-market-tape__note {
        padding-inline: 8px;
        text-align: left;
      }
    }
  </style>

  <div class="tradingview-widget-container">
    <div class="tradingview-widget-container__widget">
      <div class="ohc-live-market-tape__loading">Loading provider market data…</div>
    </div>
    <script
      type="text/javascript"
      src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js"
      async
    >
      {
        "symbols": [
          { "proName": "FX:EURUSD", "title": "EUR/USD" },
          { "proName": "FX:GBPUSD", "title": "GBP/USD" },
          { "proName": "FX:USDJPY", "title": "USD/JPY" },
          { "proName": "CMCMARKETS:GOLD", "title": "Gold CFD" },
          { "proName": "BITSTAMP:BTCUSD", "title": "BTC/USD" },
          { "proName": "FOREXCOM:SPXUSD", "title": "S&P 500 CFD" },
          { "proName": "FOREXCOM:NSXUSD", "title": "Nasdaq 100 CFD" }
        ],
        "showSymbolLogo": false,
        "isTransparent": true,
        "displayMode": "regular",
        "colorTheme": "dark",
        "locale": "en"
      }
    </script>
  </div>

  <p class="ohc-live-market-tape__note">
    Data by <a href="https://www.tradingview.com/" target="_blank" rel="noopener noreferrer">TradingView</a>
    · timing varies by market · indicative only ·
    <a href="/risk-disclaimer">details</a>
  </p>

  <noscript>
    <p class="ohc-live-market-tape__note">Market data requires JavaScript. No fallback prices are displayed.</p>
  </noscript>
</div>