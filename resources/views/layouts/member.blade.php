<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member Workspace') &middot; OHC Trade Room</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="ohc-member-body">
@php
    $member = Auth::user();
    $displayName = trim(($member->first_name ?? '').' '.($member->last_name ?? '')) ?: 'Member';
    $initial = strtoupper(substr($displayName, 0, 1));
    $avatarUrl = $member->avatar_url ? asset('storage/'.$member->avatar_url) : null;
    $hasAdmin = class_exists(\App\Support\AdminAccess::class) && \App\Support\AdminAccess::allows($member, 'admin.view');
    $unreadCount = $member->unreadNotifications()->count();
    $navLinks = [
        ['Home', route('dashboard'), request()->routeIs('dashboard')],
        ['Markets', route('markets.index'), request()->routeIs('markets.*')],
        ['Learn', route('learn.index'), request()->routeIs('learn.*') || request()->routeIs('courses.*')],
        ['Trade Room', route('trade-room.index'), request()->routeIs('trade-room.*')],
        ['Journal', route('journal.index'), request()->routeIs('journal.*')],
        ['Portfolio', route('portfolio.index'), request()->routeIs('portfolio.*')],
        ['Research', route('research.index'), request()->routeIs('research.*')],
        ['Community', route('community.index'), request()->routeIs('community.*')],
    ];
@endphp

<header class="ohc-member-header">
    <div class="ohc-dashboard-container">
        <nav class="ohc-member-nav" aria-label="Member navigation">
            <a href="{{ route('dashboard') }}" class="ohc-member-brand" aria-label="OHC Trade Room home"><x-brand-logo variant="light" /></a>
            <div class="ohc-member-nav__links">
                @foreach ($navLinks as [$label, $url, $active])<a href="{{ $url }}" class="{{ $active ? 'is-active' : '' }}">{{ $label }}</a>@endforeach
            </div>
            <div class="ohc-member-actions">
                @if ($hasAdmin)<a href="{{ route('admin.dashboard') }}" class="ohc-admin-shortcut">Admin</a>@endif
                <a href="{{ route('notifications.index') }}" class="ohc-notification-bell {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}" aria-label="Notifications{{ $unreadCount ? ': '.$unreadCount.' unread' : '' }}">
                    <span aria-hidden="true">&#128276;</span>@if($unreadCount)<b>{{ min($unreadCount, 99) }}</b>@endif
                </a>
                <details class="ohc-member-menu">
                    <summary class="ohc-member-avatar" title="{{ $displayName }}">@if ($avatarUrl)<img src="{{ $avatarUrl }}" alt="{{ $displayName }} profile photo">@else<span>{{ $initial }}</span>@endif</summary>
                    <div>
                        <strong>{{ $displayName }}</strong><small>{{ $member->email }}</small>
                        <a href="{{ route('profile.edit') }}">Profile &amp; settings</a>
                        @if ($hasAdmin)<a href="{{ route('admin.dashboard') }}">Admin workspace</a>@endif
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
                    </div>
                </details>
            </div>
        </nav>
    </div>
</header>

@if (session('status'))<div class="workspace-flash" role="status">{{ session('status') }}</div>@endif
@if ($errors->any())<div class="workspace-flash is-error" role="alert">Please review the highlighted fields and try again.</div>@endif

<main>@yield('content')</main>

<nav class="ohc-mobile-tabs" aria-label="Mobile member navigation">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}"><span>Home</span></a>
    <a href="{{ route('markets.index') }}" class="{{ request()->routeIs('markets.*') ? 'is-active' : '' }}"><span>Markets</span></a>
    <a href="{{ route('learn.index') }}" class="{{ request()->routeIs('learn.*') ? 'is-active' : '' }}"><span>Learn</span></a>
    <a href="{{ route('journal.index') }}" class="{{ request()->routeIs('journal.*') ? 'is-active' : '' }}"><span>Journal</span></a>
    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'is-active' : '' }}"><span>More</span></a>
</nav>

@stack('scripts')
</body>
</html>
