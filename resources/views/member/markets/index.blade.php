@extends('layouts.member')
@section('title', 'Markets')
@section('content')
<div class="workspace-canvas"><div class="ohc-dashboard-container">
    <x-workspace-heading eyebrow="Daily market intelligence" title="Markets" description="Live provider data, today's economic events, and OHC-published market context.">
        <x-slot:actions><a class="workspace-button is-secondary" href="{{ route('research.index') }}">Open Research</a></x-slot:actions>
    </x-workspace-heading>

    <div class="workspace-grid markets-top-grid">
        <section class="workspace-card markets-board-card">
            <div class="workspace-card__heading"><div><p>Global markets</p><h2>Live market board</h2></div><span class="live-badge">Live provider data</span></div>
            <div class="tradingview-widget-container market-widget-tall"><div class="tradingview-widget-container__widget"></div><script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>{!! json_encode(['colorTheme'=>'light','dateRange'=>'1D','showChart'=>true,'locale'=>'en','width'=>'100%','height'=>560,'isTransparent'=>true,'showSymbolLogo'=>true,'tabs'=>[['title'=>'Indices','symbols'=>[['s'=>'FOREXCOM:SPXUSD','d'=>'S&P 500'],['s'=>'FOREXCOM:NSXUSD','d'=>'Nasdaq 100'],['s'=>'DJ:DJI','d'=>'Dow'],['s'=>'TVC:UKX','d'=>'FTSE 100'],['s'=>'TVC:NI225','d'=>'Nikkei 225']]],['title'=>'Macro','symbols'=>[['s'=>'TVC:US10Y','d'=>'US 10Y'],['s'=>'TVC:DXY','d'=>'US Dollar'],['s'=>'OANDA:XAUUSD','d'=>'Gold'],['s'=>'TVC:USOIL','d'=>'WTI Oil'],['s'=>'BITSTAMP:BTCUSD','d'=>'Bitcoin'],['s'=>'CBOE:VIX','d'=>'VIX']]]]], JSON_UNESCAPED_SLASHES) !!}</script></div>
        </section>
        <aside class="workspace-stack">
            <section class="workspace-card market-context-card"><p>OHC market context</p>
                @if($gamePlan)<h2>{{ $gamePlan->market ?: 'Today' }}: {{ $gamePlan->title }}</h2><span>{{ $gamePlan->bias ?: 'No directional bias has been published.' }}</span><a href="{{ route('research.index') }}">View game plan</a>
                @else<h2>No market brief published yet</h2><span>The Admin publishing desk can add today's structured game plan. No automated interpretation is being substituted.</span>@endif
            </section>
            <section class="workspace-card"><div class="workspace-card__heading"><div><p>OHC reports</p><h2>Latest research</h2></div></div>
                <div class="compact-list">@forelse($reports as $report)<a href="{{ route('research.report',$report) }}"><strong>{{ $report->title }}</strong><span>{{ $report->published_at->format('j M Y') }}</span></a>@empty<p class="muted-copy">No published research reports yet.</p>@endforelse</div>
            </section>
        </aside>
    </div>

    <div class="workspace-grid markets-lower-grid">
        <section class="workspace-card"><div class="workspace-card__heading"><div><p>Market breadth</p><h2>S&amp;P 500 heatmap</h2></div></div><div class="tradingview-widget-container market-widget-medium"><div class="tradingview-widget-container__widget"></div><script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-stock-heatmap.js" async>{!! json_encode(['exchanges'=>[],'dataSource'=>'SPX500','grouping'=>'sector','blockSize'=>'market_cap_basic','blockColor'=>'change','locale'=>'en','symbolUrl'=>'','colorTheme'=>'light','hasTopBar'=>false,'isDataSetEnabled'=>false,'isZoomEnabled'=>true,'hasSymbolTooltip'=>true,'isMonoSize'=>false,'width'=>'100%','height'=>470], JSON_UNESCAPED_SLASHES) !!}</script></div></section>
        <section class="workspace-card"><div class="workspace-card__heading"><div><p>Member timezone: {{ $timezone }}</p><h2>Economic calendar</h2></div></div><div class="tradingview-widget-container market-widget-medium"><div class="tradingview-widget-container__widget"></div><script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-events.js" async>{!! json_encode(['colorTheme'=>'light','isTransparent'=>true,'width'=>'100%','height'=>470,'locale'=>'en','importanceFilter'=>'0,1','countryFilter'=>'us,gb,eu,jp,cn,ca,au,nz,ch'], JSON_UNESCAPED_SLASHES) !!}</script></div></section>
    </div>
    <p class="workspace-disclaimer">Market data is supplied by TradingView and may be delayed according to the exchange and your location. It is indicative only and is not investment advice.</p>
</div></div>
@endsection
