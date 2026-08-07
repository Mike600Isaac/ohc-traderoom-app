@extends('layouts.member')

@section('content')
{{-- Include page-specific CSS --}}
@push('styles')
    @vite(['resources/css/courses.css'])
@endpush

<div class="bg-[#f8fafc] min-h-screen pb-20">
    <div class="app-container pt-12">
        
        {{-- Page Header --}}
        <header class="mb-10">
            <h1 class="text-3xl font-bold text-[#273a68] mb-2">My Courses</h1>
            <p class="text-gray-500">Only courses included in your active bundle or standalone purchase are unlocked.</p>
        </header>

        {{-- Category Filter Tabs --}}
        <div class="flex flex-wrap gap-3 mb-10">
            <button class="filter-tab active">All Courses</button>
            <button class="filter-tab">Foundations</button>
            <button class="filter-tab">Asset Analysis</button>
            <button class="filter-tab">Advanced Strategy</button>
            <button class="filter-tab">Trading System</button>
            <button class="filter-tab">Live & Coaching</button>
        </div>

        {{-- Course Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach ($courses as $course)
                <div class="glass-card !p-0 overflow-hidden group flex flex-col {{ !$course['is_owned'] ? 'course-card--locked' : '' }}">
                    
                    {{-- Thumbnail Area --}}
                    <div class="h-44 bg-gray-200 relative overflow-hidden card-thumbnail">
                        @if($course['is_owned'])
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center group-hover:border-[#2394a0] transition">
                                    <svg class="w-5 h-5 text-gray-400 ml-0.5 group-hover:text-[#2394a0] transition" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        @else
                            <div class="lock-overlay">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Content Area --}}
                    <div class="p-6 flex-grow flex flex-col">
                        <span class="text-[#2394a0] text-[10px] font-extrabold tracking-widest uppercase mb-1">{{ $course['category'] }}</span>
                        <h4 class="font-bold text-[#273a68] text-lg leading-tight mb-2">{{ $course['title'] }}</h4>
                        <p class="course-access-note">Included in: {{ $course['included_in'] }}</p>
                        
                        <div class="mt-auto">
                            @if($course['is_owned'])
                                {{-- Progress State --}}
                                <p class="text-[11px] text-gray-400 mb-2">{{ is_numeric($course['modules']) ? $course['modules'] . ' modules' : $course['modules'] }}</p>
                                @if($course['progress'] !== null)
                                    <div class="w-full bg-gray-200 h-1.5 rounded-full">
                                        <div class="bg-[#2394a0] h-full rounded-full" style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-2 font-bold">{{ $course['progress'] }}% complete</p>
                                @else
                                    <span class="course-owned-pill">Access active · no progress recorded</span>
                                @endif
                            @else
                                {{-- Locked State --}}
                                <div class="flex justify-between items-center gap-3">
                                    <a href="{{ $course['unlock_url'] }}" class="btn-unlock">Unlock</a>
                                    <span class="text-[#273a68] font-bold text-sm">{{ $course['price'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection