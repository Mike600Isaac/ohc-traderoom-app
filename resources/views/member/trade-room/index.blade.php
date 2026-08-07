@extends('layouts.member')
@section('title', 'Trade Room')
@section('content')
<div class="workspace-canvas"><div class="ohc-dashboard-container">
    <x-workspace-heading eyebrow="Earn" title="Live Trade Room" description="Join published sessions, manage reminders, and watch available replays." />
    <section class="live-room-hero {{ $hasAccess ? '' : 'is-locked' }}">
        @if($nextSession)
            <div><p>{{ $nextSession->starts_at->isPast() ? 'Live or in progress' : 'Next live session' }}</p><h1>{{ $nextSession->title }}</h1><span>{{ $nextSession->starts_at->timezone(auth()->user()->timezone)->format('l, j F · g:i A T') }}</span><p>{{ $nextSession->agenda ?: 'Agenda not published.' }}</p></div>
            <div class="live-room-hero__action"><strong data-session-countdown="{{ $nextSession->starts_at->toIso8601String() }}">-- : -- : --</strong>@if($hasAccess && $nextSession->join_url)<a class="workspace-button" href="{{ $nextSession->join_url }}" target="_blank" rel="noopener">Join session</a>@elseif(!$hasAccess)<a class="workspace-button" href="{{ route('courses.index') }}">View Trader access</a>@else<span>Join link not published</span>@endif</div>
        @else<div><p>Live Trade Room</p><h1>No session is currently scheduled</h1><span>The schedule updates automatically when the Admin team publishes a session.</span></div>@endif
    </section>
    <div class="workspace-grid trade-room-grid">
        <section class="workspace-card"><div class="workspace-card__heading"><div><p>Calendar</p><h2>Upcoming schedule</h2></div></div><div class="session-list">@forelse($upcoming as $session)<article><time><strong>{{ $session->starts_at->timezone(auth()->user()->timezone)->format('d') }}</strong><span>{{ $session->starts_at->timezone(auth()->user()->timezone)->format('M') }}</span></time><div><h3>{{ $session->title }}</h3><p>{{ $session->starts_at->timezone(auth()->user()->timezone)->format('g:i A T') }}{{ $session->host ? ' · '.$session->host->first_name : '' }}</p></div><form method="POST" action="{{ route('trade-room.reminder',$session) }}">@csrf<button class="text-button" type="submit">{{ $reminderIds->contains($session->id) ? 'Reminder set' : 'Remind me' }}</button></form></article>@empty<p class="muted-copy">No upcoming sessions have been published.</p>@endforelse</div></section>
        <section class="workspace-card"><div class="workspace-card__heading"><div><p>Library</p><h2>Recent replays</h2></div></div><div class="replay-list">
            @forelse($replays as $replay)
                <article><div><strong>{{ $replay->title }}</strong><span>{{ $replay->starts_at->format('j M Y') }}</span></div>
                    @if($hasAccess)<a href="{{ $replay->replay_url }}" target="_blank" rel="noopener">Watch replay</a>@else<span class="locked-label">Path required</span>@endif
                    @if($replay->recap)<p>{{ Str::limit($replay->recap,140) }}</p>@endif
                </article>
            @empty
                <p class="muted-copy">No replay links have been published.</p>
            @endforelse
        </div></section>
    </div>
    <section class="workspace-card signal-placeholder"><div><p>LMRSS signal alerts</p><h2>Signal feed not connected</h2><span>No simulated or fabricated signals are shown. This area activates only when a verified real-time signal publisher is connected.</span></div><span class="locked-label">Ultimate feature</span></section>
</div></div>
@endsection
@push('scripts')<script>document.addEventListener('DOMContentLoaded',function(){var el=document.querySelector('[data-session-countdown]');if(!el)return;var target=new Date(el.dataset.sessionCountdown).getTime();function tick(){var d=Math.max(0,target-Date.now());el.textContent=[Math.floor(d/3600000),Math.floor((d%3600000)/60000),Math.floor((d%60000)/1000)].map(function(v){return String(v).padStart(2,'0')}).join(' : ')}tick();setInterval(tick,1000)});</script>@endpush
