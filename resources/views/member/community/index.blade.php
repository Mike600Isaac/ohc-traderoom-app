@extends('layouts.member')
@section('title', 'Community')
@section('content')
<div class="workspace-canvas"><div class="ohc-dashboard-container">
    <x-workspace-heading eyebrow="Professional community" title="Community" description="Structured channels for questions, analysis, reviews, and mentorship - governed by your Path access." />
    <div class="community-layout"><section><div class="workspace-section__heading"><h2>Channels</h2><span>{{ $channels->count() }} published</span></div><div class="channel-grid">
        @forelse($channels as $channel)
            <article class="channel-card {{ $channel->member_has_access ? '' : 'is-locked' }}"><span>{{ strtoupper(substr($channel->name,0,2)) }}</span><div><h2>{{ $channel->name }}</h2><p>{{ $channel->description ?: 'Channel description has not been published.' }}</p><small>{{ $channel->posts_count }} {{ Str::plural('post',$channel->posts_count) }} @if($channel->required_path) &middot; {{ $channel->required_path }} Path @endif</small></div>@if($channel->member_has_access)<a href="{{ route('community.channel',$channel) }}">Open channel</a>@else<a href="{{ route('courses.index') }}">View access</a>@endif</article>
        @empty
            <x-workspace-empty title="No channels published" copy="Community channels will appear after the Admin team publishes them." />
        @endforelse
    </div></section><aside class="workspace-card community-guidelines"><p>Community standard</p><h2>Professional, useful, accountable</h2><ul><li>Explain the reasoning behind a market view.</li><li>Protect personal and account information.</li><li>No guaranteed returns or unverified signals.</li><li>Keep feedback constructive and specific.</li></ul></aside></div>
</div></div>
@endsection
