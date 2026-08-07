@extends('layouts.member')
@section('title', 'Home Workspace')

@section('content')
@php
    $timezone = $user->timezone ?? 'Africa/Lagos';
    $localNow = now($timezone);
    $hour = (int) $localNow->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $displayName = $user->first_name ?: $user->last_name ?: 'Member';
    $daysRemaining = $accessEndsAt ? max(0, $localNow->diffInDays($accessEndsAt, false)) : null;
    $pathLabel = $currentPath ? strtoupper($currentPath) : 'NO ACTIVE PATH';
    $journeySteps = ['Learn', 'Earn', 'Protect', 'Grow'];
    $journeyCurrent = $todayFocus ? 0 : null;
    $goalPercent = $weeklyGoal && $weeklyGoal->target > 0
        ? min(100, (int) round(($weeklyGoal->completed / $weeklyGoal->target) * 100))
        : null;
    $primaryPlanUrl = $gamePlan?->video_url ?: $gamePlan?->pdf_url ?: $gamePlan?->chart_url;
@endphp

<div class="ohc-home-workspace">
    <section class="ohc-workspace-hero">
        <div class="ohc-dashboard-container">
            <div class="ohc-workspace-greeting">
                <div>
                    <h1><span data-local-greeting>{{ $greeting }}</span>, {{ $displayName }}</h1>
                    <p>
                        <span data-local-dashboard-time>{{ $localNow->format('l j F · g:i A T') }}</span>
                        <i></i><span>Live provider market data below</span>
                        <i></i><span>Your account values use recorded OHC data only</span>
                    </p>
                </div>
                <span class="ohc-path-pill">
                    {{ $pathLabel }}
                    @if ($daysRemaining !== null)
                        · {{ $daysRemaining }} days
                    @elseif ($activeEntitlements->isNotEmpty())
                        · ACTIVE
                    @endif
                </span>
            </div>

            <ol class="ohc-journey" aria-label="Financial journey">
                @foreach ($journeySteps as $index => $step)
                    <li class="{{ $journeyCurrent === $index ? 'is-current' : '' }}">
                        <span>{{ $journeyCurrent !== null && $index < $journeyCurrent ? '✓' : $index + 1 }}</span>
                        <strong>{{ $step }}</strong>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <div class="ohc-dashboard-container ohc-workspace-content">
        <section class="ohc-status-grid" aria-label="Member status">
            <article class="ohc-status-card ohc-status-card--teal">
                <p>Today's Focus</p>
                <h2>{{ $todayFocus['title'] ?? 'No activity yet' }}</h2>
                @if ($todayFocus)
                    <span>{{ $todayFocus['progress'] !== null ? $todayFocus['progress'].'% recorded progress' : 'Ready to continue' }}</span>
                @else
                    <a href="{{ route('courses.index') }}">Choose a course</a>
                @endif
            </article>
            <article class="ohc-status-card ohc-status-card--green">
                <p>Portfolio Health</p>
                <h2>Not connected</h2>
                <span>No holdings recorded</span>
            </article>
            <article class="ohc-status-card ohc-status-card--purple">
                <p>Consistency</p>
                <h2>No score yet</h2>
                <span>Awaiting activity history</span>
            </article>
            <article class="ohc-status-card ohc-status-card--amber">
                <p>Weekly Goal</p>
                @if ($weeklyGoal)
                    <h2>{{ $weeklyGoal->completed }} / {{ $weeklyGoal->target }}</h2>
                    <span>{{ max(0, $weeklyGoal->target - $weeklyGoal->completed) }} activities to go</span>
                @else
                    <h2>Not set</h2>
                    <span>No target recorded</span>
                @endif
            </article>
        </section>

        <div class="ohc-market-session-row">
            <x-dashboard-market-grid />

            <section class="ohc-session-card" aria-labelledby="next-session-title">
                <p class="ohc-session-label"><i></i> Next Live Session</p>
                @if ($nextSession)
                    <strong class="ohc-session-countdown" data-session-countdown="{{ $nextSession->starts_at->toIso8601String() }}">-- : -- : --</strong>
                    <h2 id="next-session-title">{{ $nextSession->title }} · {{ $nextSession->starts_at->timezone($timezone)->format('g:i A T') }}</h2>
                    <ul>
                        @if ($nextSession->agenda)<li>{{ \Illuminate\Support\Str::limit($nextSession->agenda, 72) }}</li>@endif
                        @if ($nextSession->registered_count !== null)<li>{{ $nextSession->registered_count }} members registered</li>@endif
                        <li>{{ $nextSession->starts_at->timezone($timezone)->format('l, F j') }}</li>
                    </ul>
                    @if ($nextSession->join_url)<a href="{{ $nextSession->join_url }}">Join Session</a>@else<span class="ohc-session-disabled">Join link not published</span>@endif
                @else
                    <strong class="ohc-session-countdown">-- : -- : --</strong>
                    <h2 id="next-session-title">No live session scheduled</h2>
                    <ul><li>A published session will appear here</li><li>No attendance figures are estimated</li></ul>
                    <span class="ohc-session-disabled">Schedule currently empty</span>
                @endif
            </section>
        </div>

        <section class="ohc-game-plan" aria-labelledby="game-plan-title">
            <div class="ohc-panel-heading"><h2>Today's Game Plan</h2>@if($primaryPlanUrl)<a href="{{ $primaryPlanUrl }}">Open</a>@endif</div>
            @if ($gamePlan)
                <div class="ohc-game-plan__title"><h3 id="game-plan-title">{{ $gamePlan->market ?: 'Market' }} — {{ $gamePlan->title }}</h3>@if($gamePlan->published_at?->isToday())<span>New</span>@endif</div>
                <div class="ohc-game-plan__body">
                    <ul>
                        @if($gamePlan->bias)<li><i class="is-checked">✓</i> Bias: {{ $gamePlan->bias }}</li>@endif
                        @foreach(array_slice($gamePlan->key_levels ?? [], 0, 2) as $level)<li><i></i> {{ $level }}</li>@endforeach
                        @if($gamePlan->invalidation)<li><i></i> Invalidation: {{ $gamePlan->invalidation }}</li>@endif
                        @if(!empty($gamePlan->watchlist))<li><i></i> Watch: {{ implode(', ', $gamePlan->watchlist) }}</li>@endif
                    </ul>
                    <div class="ohc-game-plan__links">@if($gamePlan->video_url)<a href="{{ $gamePlan->video_url }}">Video</a>@endif @if($gamePlan->pdf_url)<a href="{{ $gamePlan->pdf_url }}">PDF</a>@endif @if($gamePlan->chart_url)<a href="{{ $gamePlan->chart_url }}">Charts</a>@endif</div>
                </div>
            @else
                <div class="ohc-game-plan__title"><h3 id="game-plan-title">No game plan published today</h3></div>
                <p class="ohc-game-plan__empty">The research desk has not published a bias, key levels, invalidation, or watchlist for today.</p>
            @endif
        </section>

        <div class="ohc-lower-grid">
            <section class="ohc-learning-reminder">
                <p>Learning Reminder</p>
                @if ($todayFocus)
                    <h2>Continue “{{ $todayFocus['title'] }}”</h2>
                    <a href="{{ $todayFocus['url'] }}">Open lesson</a>
                @else
                    <h2>No lesson activity has been recorded yet.</h2>
                    <a href="{{ route('courses.index') }}">Browse courses</a>
                @endif
            </section>
            <section class="ohc-attention-card">
                <p>Needs Your Attention</p>
                <ul>
                    @forelse($attention as $index => $item)
                        <li class="is-{{ $index + 1 }}"><i></i><span>{{ $item['title'] }}</span><a href="{{ $item['url'] }}">{{ $item['action'] }}</a></li>
                    @empty
                        <li class="is-clear"><i></i><span>You're up to date</span></li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var timeTarget = document.querySelector('[data-local-dashboard-time]');
    var greetingTarget = document.querySelector('[data-local-greeting]');
    function updateClock() {
        var now = new Date();
        if (timeTarget) timeTarget.textContent = new Intl.DateTimeFormat(undefined, { weekday: 'long', day: 'numeric', month: 'long', hour: 'numeric', minute: '2-digit', timeZoneName: 'short' }).format(now);
        if (greetingTarget) greetingTarget.textContent = now.getHours() < 12 ? 'Good morning' : (now.getHours() < 17 ? 'Good afternoon' : 'Good evening');
    }
    updateClock(); window.setInterval(updateClock, 60000);

    var countdown = document.querySelector('[data-session-countdown]');
    if (countdown) {
        var target = new Date(countdown.dataset.sessionCountdown).getTime();
        function updateCountdown() {
            var distance = Math.max(0, target - Date.now());
            var hours = Math.floor(distance / 3600000);
            var minutes = Math.floor((distance % 3600000) / 60000);
            var seconds = Math.floor((distance % 60000) / 1000);
            countdown.textContent = [hours, minutes, seconds].map(function (value) { return String(value).padStart(2, '0'); }).join(' : ');
        }
        updateCountdown(); window.setInterval(updateCountdown, 1000);
    }
});
</script>
@endpush
