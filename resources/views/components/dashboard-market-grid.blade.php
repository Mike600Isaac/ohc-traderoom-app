@php
    $symbols = [
        ['symbol' => 'FOREXCOM:SPXUSD', 'label' => 'S&P 500'],
        ['symbol' => 'FOREXCOM:NSXUSD', 'label' => 'NASDAQ 100'],
        ['symbol' => 'DJ:DJI', 'label' => 'DOW'],
        ['symbol' => 'AMEX:IWM', 'label' => 'RUSSELL 2000'],
        ['symbol' => 'TVC:US10Y', 'label' => 'US 10Y'],
        ['symbol' => 'TVC:DXY', 'label' => 'DXY'],
        ['symbol' => 'OANDA:XAUUSD', 'label' => 'GOLD'],
        ['symbol' => 'BITSTAMP:BTCUSD', 'label' => 'BTC'],
    ];
@endphp

<section class="ohc-market-panel" aria-labelledby="today-markets-title">
    <div class="ohc-panel-heading">
        <h2 id="today-markets-title">Today's Markets</h2>
        <span>Live board</span>
    </div>
    <div class="ohc-market-grid">
        @foreach ($symbols as $item)
            <article class="ohc-market-cell" aria-label="Live {{ $item['label'] }} market data">
                <span class="ohc-market-cell__label">{{ $item['label'] }}</span>
                <div class="tradingview-widget-container">
                    <div class="tradingview-widget-container__widget">
                        <span class="ohc-market-loading">Connecting…</span>
                    </div>
                    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
                    {!! json_encode([
                        'symbol' => $item['symbol'],
                        'width' => '100%',
                        'height' => '94',
                        'locale' => 'en',
                        'dateRange' => '1D',
                        'colorTheme' => 'light',
                        'isTransparent' => true,
                        'autosize' => false,
                        'largeChartUrl' => '',
                    ], JSON_UNESCAPED_SLASHES) !!}
                    </script>
                </div>
            </article>
        @endforeach
    </div>
    <footer class="ohc-market-panel__footer">
        <span>Data by TradingView · timing varies by market · indicative only</span>
        <a href="{{ route('legal.risk-disclaimer') }}">Details</a>
    </footer>
    <noscript><p class="ohc-market-noscript">Live market data requires JavaScript. No fallback prices are displayed.</p></noscript>
</section>
