@extends('layouts.admin')
@section('title', 'Overview')
@section('heading', 'Operations Overview')
@section('content')
@php
    $cards = [
        ['label' => 'Active members', 'value' => $metrics['members_active'], 'note' => $metrics['members_total'].' total member accounts'],
        ['label' => 'Needs verification', 'value' => $metrics['members_unverified'], 'note' => 'Email not verified'],
        ['label' => 'Members at risk', 'value' => $metrics['members_at_risk'], 'note' => 'No login in 30 days'],
        ['label' => 'Active entitlements', 'value' => $metrics['active_entitlements'], 'note' => 'Not expired'],
        ['label' => 'Learning active', 'value' => $metrics['learning_active_7d'], 'note' => 'Members active in 7 days'],
        ['label' => 'Upcoming sessions', 'value' => $metrics['upcoming_sessions'], 'note' => 'Scheduled from today'],
        ['label' => 'Draft game plans', 'value' => $metrics['game_plans_draft'], 'note' => 'Awaiting publication'],
        ['label' => 'Successful payments', 'value' => $metrics['payments_successful'], 'note' => number_format($metrics['revenue_minor_units'] / 100, 2).' recorded minor-unit total'],
    ];
@endphp
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($cards as $card)<article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-500">{{ $card['label'] }}</p><p class="mt-3 text-3xl font-black text-[#10203d]">{{ $card['value'] }}</p><p class="mt-2 text-xs font-semibold text-slate-500">{{ $card['note'] }}</p></article>@endforeach
</div>

<div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4"><div><p class="text-[10px] font-black uppercase tracking-[0.18em] text-teal-700">Accountability</p><h2 class="mt-1 text-xl font-black text-[#10203d]">Recent admin activity</h2></div>@if(\App\Support\AdminAccess::allows(auth()->user(), 'audit.view'))<a href="{{ route('admin.audit.index') }}" class="text-sm font-black text-teal-700">View all →</a>@endif</div>
        <div class="mt-5 divide-y divide-slate-100">
            @forelse ($metrics['recent_audits'] as $log)<div class="py-4"><div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"><p class="font-bold text-[#10203d]">{{ $log->summary }}</p><time class="text-xs font-semibold text-slate-400">{{ $log->created_at->diffForHumans() }}</time></div><p class="mt-1 text-xs text-slate-500">{{ $log->actor?->email ?? 'System' }} · {{ $log->action }}</p></div>@empty<p class="py-8 text-sm text-slate-500">No administrative actions have been recorded yet.</p>@endforelse
        </div>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-[#10203d] p-6 text-white shadow-sm"><p class="text-[10px] font-black uppercase tracking-[0.18em] text-teal-300">Quick publish</p><h2 class="mt-2 text-xl font-black">Operate today's member experience</h2><div class="mt-5 space-y-3">@if(\App\Support\AdminAccess::allows(auth()->user(), 'publishing.manage'))<a href="{{ route('admin.game-plans.create') }}" class="block rounded-lg bg-teal-600 px-4 py-3 text-center text-sm font-black hover:bg-teal-500">Create game plan</a><a href="{{ route('admin.reports.create') }}" class="block rounded-lg border border-white/20 px-4 py-3 text-center text-sm font-black hover:bg-white/10">Create market report</a>@endif @if(\App\Support\AdminAccess::allows(auth()->user(), 'sessions.manage'))<a href="{{ route('admin.sessions.create') }}" class="block rounded-lg border border-white/20 px-4 py-3 text-center text-sm font-black hover:bg-white/10">Schedule live session</a>@endif</div></section>
</div>
@endsection
