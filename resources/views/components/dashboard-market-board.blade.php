<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="market-board-title">
    <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-teal-700">Provider market feed</p>
            <h2 id="market-board-title" class="mt-1 text-xl font-extrabold text-[#10203d]">Today's Markets</h2>
        </div>
        <p class="text-xs font-semibold text-slate-500">Live provider data · timing varies by market</p>
    </div>
    <div class="min-h-[430px] bg-white">
        <div class="tradingview-widget-container h-full w-full">
            <div class="tradingview-widget-container__widget h-full w-full">
                <p class="grid min-h-[430px] place-items-center text-sm font-bold text-slate-500">Connecting to TradingView…</p>
            </div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>
            {
              "colorTheme": "light",
              "dateRange": "1D",
              "showChart": true,
              "locale": "en",
              "width": "100%",
              "height": "430",
              "largeChartUrl": "",
              "isTransparent": true,
              "showSymbolLogo": false,
              "showFloatingTooltip": true,
              "plotLineColorGrowing": "rgba(15, 118, 110, 1)",
              "plotLineColorFalling": "rgba(190, 24, 93, 1)",
              "gridLineColor": "rgba(226, 232, 240, 1)",
              "scaleFontColor": "rgba(71, 85, 105, 1)",
              "belowLineFillColorGrowing": "rgba(20, 184, 166, 0.12)",
              "belowLineFillColorFalling": "rgba(244, 63, 94, 0.12)",
              "tabs": [
                {
                  "title": "Indices",
                  "symbols": [
                    { "s": "FOREXCOM:SPXUSD", "d": "S&P 500" },
                    { "s": "FOREXCOM:NSXUSD", "d": "Nasdaq 100" },
                    { "s": "DJ:DJI", "d": "Dow 30" },
                    { "s": "CBOE:VIX", "d": "Volatility" }
                  ],
                  "originalTitle": "Indices"
                },
                {
                  "title": "Rates & FX",
                  "symbols": [
                    { "s": "TVC:US10Y", "d": "US 10Y Yield" },
                    { "s": "TVC:DXY", "d": "US Dollar Index" },
                    { "s": "FX:EURUSD", "d": "EUR/USD" },
                    { "s": "FX:GBPUSD", "d": "GBP/USD" }
                  ],
                  "originalTitle": "Rates & FX"
                },
                {
                  "title": "Metals & Crypto",
                  "symbols": [
                    { "s": "OANDA:XAUUSD", "d": "Gold" },
                    { "s": "OANDA:XAGUSD", "d": "Silver" },
                    { "s": "BITSTAMP:BTCUSD", "d": "Bitcoin" },
                    { "s": "BITSTAMP:ETHUSD", "d": "Ethereum" }
                  ],
                  "originalTitle": "Metals & Crypto"
                }
              ]
            }
            </script>
        </div>
    </div>
    <p class="border-t border-slate-100 px-5 py-3 text-xs leading-5 text-slate-500">
        Data supplied by TradingView and may be real-time or delayed depending on the exchange. Indicative only; see the
        <a href="{{ route('legal.risk-disclaimer') }}" class="font-bold text-teal-700 underline">risk disclaimer</a>.
    </p>
</section>