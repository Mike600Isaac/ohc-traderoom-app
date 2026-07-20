@extends('layouts.member')

@section('content')
@php
    $paths = ['Foundation', 'Trader', 'Investor', 'Ultimate'];
    $currentPath = Auth::user()->current_path;
    $isBundle = in_array($currentPath, $paths);
    $label = $isBundle ? 'PATH' : 'PLAN';
@endphp

{{-- 1. Hero / Journey Banner --}}
<section class="bg-[#0f1e3a] py-12 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-2">Welcome back, {{ Auth::user()->first_name ?? Auth::user()->name }}</h1>
                <p class="text-gray-400 text-lg">
                    You're on the <span class="text-white font-bold">{{ strtoupper($currentPath) }} {{ $label }}</span> 
                    <span class="mx-2">·</span> Stage 2 of 4 of your journey to financial freedom
                </p>
            </div>
            <div>
                <span class="bg-[#2394a0] px-6 py-2 rounded-full font-bold text-sm tracking-widest">
                    {{ strtoupper($currentPath) }} {{ $isBundle ? 'PATH' : '' }}
                </span>
            </div>
        </div>

        {{-- Journey Stepper (Tailwind Flex) --}}
        <div class="mt-12 flex items-center w-full max-w-3xl">
            <div class="flex items-center text-[#2394a0] font-bold">
                <div class="w-8 h-8 rounded-full bg-[#2394a0] text-white flex items-center justify-center mr-3">✓</div>
                LEARN
            </div>
            <div class="flex-1 h-px bg-gray-700 mx-4"></div>
            <div class="flex items-center text-[#2394a0] font-bold">
                <div class="w-8 h-8 rounded-full bg-[#2394a0] text-white flex items-center justify-center mr-3">2</div>
                EARN
            </div>
            <div class="flex-1 h-px bg-gray-700 mx-4"></div>
            <div class="text-gray-600 font-bold">PROTECT</div>
            <div class="flex-1 h-px bg-gray-700 mx-4"></div>
            <div class="text-gray-600 font-bold">GROW</div>
        </div>
    </div>
</section>

{{-- Content below the hero sits on its own off-white section — it no longer
     overlaps the dark hero, which is what was breaking the translucent
     .glass-card (it was picking up the navy bg behind it via -mt-8). --}}
<div class="bg-[#f8fafc]">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
    {{-- 2. Continue Learning (Using your .glass-card from style.css) --}}
    <h2 class="text-xl font-bold text-[#273a68] mb-4">Continue Learning</h2>
    <div class="glass-card flex flex-col md:flex-row items-center gap-8 mb-12">
        <div class="w-full md:w-64 aspect-video bg-black rounded-xl flex items-center justify-center relative overflow-hidden group cursor-pointer">
            <div class="play-btn w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl group-hover:bg-[#2394a0] transition">
                <svg class="w-6 h-6 text-[#273a68] group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
        </div>
        <div class="flex-1">
            <span class="text-[#2394a0] text-xs font-extrabold tracking-widest uppercase">LMRSS Day Trading System</span>
            <h3 class="text-xl font-bold text-[#273a68] mt-1 mb-4">Module 3 · Lesson 2 — Rotation Setups & Signal Confirmation</h3>
            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                <div class="bg-[#2394a0] h-full w-[45%]"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2 font-semibold">45% complete</p>
        </div>
        <a href="#" class="btn--teal">Resume</a>
    </div>

    {{-- 3. Today at OHC (Tailwind Grid) --}}
    <h2 class="text-2xl font-bold text-[#273a68] mb-6">Today at OHC</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass-card">
            <span class="text-red-500 font-extrabold text-xs flex items-center gap-2 mb-3">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span> LIVE TODAY
            </span>
            <h3 class="text-lg font-bold text-[#273a68]">OHC Trade Room</h3>
            <p class="text-gray-500 text-sm mb-6">Live session · 2:00pm WAT</p>
            <button class="w-full py-3 bg-[#273a68] text-white rounded-xl font-bold hover:bg-[#0f1e3a] transition">Join Live</button>
        </div>

        <div class="glass-card">
            <span class="text-[#2394a0] font-extrabold text-xs mb-3 block">WORKSHOP</span>
            <h3 class="text-lg font-bold text-[#273a68]">Derivatives Workshop</h3>
            <p class="text-gray-500 text-sm mb-6">Wed Jun 4 · 5:00pm WAT</p>
            <button class="w-full py-3 bg-[#273a68] text-white rounded-xl font-bold hover:bg-[#0f1e3a] transition">Register</button>
        </div>

        <div class="glass-card">
            <span class="text-[#2394a0] font-extrabold text-xs mb-3 block">MARKET HUB</span>
            <h3 class="text-lg font-bold text-[#273a68]">Daily Game Plan</h3>
            <p class="text-gray-500 text-sm mb-6">NDX / QQQ — Fri May 30</p>
            <button class="w-full py-3 bg-[#273a68] text-white rounded-xl font-bold hover:bg-[#0f1e3a] transition">View Report</button>
        </div>
    </div>

    {{-- 4. Your Courses --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-[#273a68]">Your Courses</h2>
        <a href="/courses" class="text-[#2394a0] font-bold hover:underline">View all &rarr;</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pb-20">
        @php
            $courses = [
                ['category' => 'Foundations',    'title' => 'Financial Markets Basics', 'progress' => 95],
                ['category' => 'Asset Analysis', 'title' => 'Equity Analysis',           'progress' => 55],
                ['category' => 'Trading System', 'title' => 'LMRSS Day Trading',         'progress' => 45],
                ['category' => 'Live',           'title' => 'Trade Room Sessions',       'progress' => null],
            ];
        @endphp
        @foreach ($courses as $course)
            <div class="glass-card !p-0 overflow-hidden group cursor-pointer">
                <div class="h-40 bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition">
                    <div class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center group-hover:border-[#2394a0] transition">
                        <svg class="w-5 h-5 text-gray-400 ml-0.5 group-hover:text-[#2394a0] transition" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
                <div class="p-5">
                    <span class="text-[#2394a0] text-[10px] font-extrabold tracking-widest uppercase">{{ $course['category'] }}</span>
                    <h4 class="font-bold text-[#273a68] mt-1 mb-3">{{ $course['title'] }}</h4>
                    @if ($course['progress'] !== null)
                        <div class="w-full bg-gray-200 h-1.5 rounded-full">
                            <div class="bg-[#2394a0] h-full rounded-full" style="width: {{ $course['progress'] }}%"></div>
                        </div>
                    @else
                        <span class="inline-block bg-gray-100 text-[#273a68] text-xs font-bold px-3 py-1.5 rounded-full">Open Live Room</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
</div>
@endsection