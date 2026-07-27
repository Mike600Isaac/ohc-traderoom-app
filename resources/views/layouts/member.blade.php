<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OHC Member Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-white font-sans antialiased">
    @php
        // Simple active-link detection so "Dashboard" (or whichever section
        // you're on) gets the teal underline state shown in the concept.
        $navLinks = [
            ['label' => 'Dashboard',       'href' => '/dashboard',   'active' => request()->is('dashboard')],
            ['label' => 'My Courses',      'href' => '/courses',     'active' => request()->is('courses*')],
            ['label' => 'Live Trade Room', 'href' => '/live',        'active' => request()->is('live*')],
            ['label' => 'Workshops',       'href' => '/workshops',   'active' => request()->is('workshops*')],
            ['label' => 'Market Hub',      'href' => '/market-hub',  'active' => request()->is('market-hub*')],
            ['label' => 'Community',       'href' => '/community',   'active' => request()->is('community*')],
            ['label' => 'Profile',         'href' => '/profile',     'active' => request()->is('profile')],
        ];

        $displayName = trim((Auth::user()->first_name ?? '') . ' ' . (Auth::user()->last_name ?? '')) ?: (Auth::user()->name ?? 'Member');
        $initial = strtoupper(substr($displayName, 0, 1));
        $avatarUrl = Auth::user()->avatar_url ? asset('storage/' . Auth::user()->avatar_url) : null;
    @endphp

    {{-- Member portal nav: deliberately NOT using the shared `.nav` class
         (that one is white/light and belongs to the public marketing site).
         This merges seamlessly with the dark hero below, like the concept. --}}
    <nav class="sticky top-0 z-50 bg-[#0f1e3a] border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-20">

            {{-- Logo: coded (SVG + text), not an <img>, so it never depends on
                 an asset existing and always renders correctly on the dark nav. --}}
            <a href="/" class="flex items-center gap-2.5 flex-shrink-0">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 17a9 9 0 0 1 18 0" stroke="#2394A0" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                <span class="flex items-baseline gap-2">
                    <span class="text-white font-extrabold text-xl tracking-tight">OHC</span>
                    <span class="text-gray-400 text-[11px] font-semibold tracking-[0.15em] uppercase">Trade Room</span>
                </span>
            </a>

            {{-- Nav links --}}
            <div class="hidden md:flex items-center space-x-8">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}"
                       class="font-semibold text-sm transition pb-1 border-b-2
                              {{ $link['active']
                                    ? 'text-[#2394a0] border-[#2394a0]'
                                    : 'text-gray-300 border-transparent hover:text-white' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Member profile avatar --}}
            <div class="flex items-center flex-shrink-0">
                <a href="/profile" class="user-avatar-btn member-nav-avatar {{ request()->is('profile') ? 'ring-2 ring-[#2394a0] ring-offset-2 ring-offset-[#0f1e3a]' : '' }}" title="{{ $displayName }}">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $displayName }} profile photo">
                    @else
                        <span>{{ $initial }}</span>
                    @endif
                </a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
