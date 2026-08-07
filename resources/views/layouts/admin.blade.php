<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · OHC Trade Room</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ohc-admin-body bg-slate-100 text-slate-700 antialiased">
@php
    $user = auth()->user();
    $links = [
        ['label' => 'Overview', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'permission' => 'admin.view'],
        ['label' => 'Members', 'route' => 'admin.members.index', 'pattern' => 'admin.members.*', 'permission' => 'members.view'],
        ['label' => 'Game Plans', 'route' => 'admin.game-plans.index', 'pattern' => 'admin.game-plans.*', 'permission' => 'publishing.view'],
        ['label' => 'Market Reports', 'route' => 'admin.reports.index', 'pattern' => 'admin.reports.*', 'permission' => 'publishing.view'],
        ['label' => 'Glossary', 'route' => 'admin.glossary.index', 'pattern' => 'admin.glossary.*', 'permission' => 'publishing.view'],
        ['label' => 'Live Sessions', 'route' => 'admin.sessions.index', 'pattern' => 'admin.sessions.*', 'permission' => 'sessions.view'],
        ['label' => 'Learning', 'route' => 'admin.courses.index', 'pattern' => 'admin.courses.*|admin.lessons.*', 'permission' => 'content.view'],
        ['label' => 'Community', 'route' => 'admin.community.index', 'pattern' => 'admin.community.*', 'permission' => 'community.view'],
        ['label' => 'Analytics', 'route' => 'admin.analytics.index', 'pattern' => 'admin.analytics.*', 'permission' => 'analytics.view'],
        ['label' => 'Audit Log', 'route' => 'admin.audit.index', 'pattern' => 'admin.audit.*', 'permission' => 'audit.view'],
        ['label' => 'Settings', 'route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'permission' => 'settings.manage'],
    ];
@endphp
<div class="min-h-screen lg:grid lg:grid-cols-[270px_minmax(0,1fr)]">
    <aside class="bg-[#10203d] text-white lg:min-h-screen">
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-6">
            <a href="{{ route('admin.dashboard') }}" class="block" aria-label="OHC Admin Workspace">
                <x-brand-logo variant="light" class="admin-brand-logo" />
            </a>
        </div>
        <nav class="flex gap-2 overflow-x-auto p-4 lg:block lg:space-y-1" aria-label="Admin navigation">
            @foreach ($links as $link)
                @if (\App\Support\AdminAccess::allows($user, $link['permission']))
                    @php $patterns = explode('|', $link['pattern']); $active = collect($patterns)->contains(fn ($pattern) => request()->routeIs($pattern)); @endphp
                    <a href="{{ route($link['route']) }}" class="block shrink-0 rounded-lg px-4 py-3 text-sm font-bold transition {{ $active ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">{{ $link['label'] }}</a>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-5 text-sm lg:mt-8">
            <p class="font-black">{{ $user->first_name }} {{ $user->last_name }}</p>
            <p class="mt-1 text-xs uppercase tracking-wider text-teal-300">{{ str_replace('_', ' ', $user->role) }}</p>
            <div class="mt-4 flex gap-4"><a href="{{ route('dashboard') }}" class="font-bold text-slate-300 hover:text-white">Member view</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="font-bold text-slate-300 hover:text-white">Sign out</button></form></div>
        </div>
    </aside>

    <main class="min-w-0">
        <header class="border-b border-slate-200 bg-white px-5 py-5 sm:px-8">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-700">OHC Operations</p><h1 class="mt-1 text-2xl font-black text-[#10203d]">@yield('heading', 'Admin Workspace')</h1></div>
                <p class="text-xs font-semibold text-slate-500">Every administrative change is audit logged</p>
            </div>
        </header>
        <div class="p-5 sm:p-8">
            @if (session('status'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</div>@endif
            @if ($errors->any())<div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><p class="font-black">Please correct the following:</p><ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
