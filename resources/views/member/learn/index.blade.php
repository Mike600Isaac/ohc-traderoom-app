@extends('layouts.member')
@section('title', 'Learning')
@section('content')
<div class="workspace-canvas"><div class="ohc-dashboard-container">
    <x-workspace-heading eyebrow="Learn" title="Learning Workspace" description="Resume your work, follow the OHC journey, and keep your progress in one place.">
        <x-slot:actions><a class="workspace-button is-secondary" href="{{ route('courses.index') }}">View full catalogue</a></x-slot:actions>
    </x-workspace-heading>
    @if($courses->isEmpty())
        <x-workspace-empty title="No courses have been published" copy="When the learning team publishes course content, it will appear here automatically."><x-slot:action><a class="workspace-button" href="{{ route('courses.index') }}">Browse available programmes</a></x-slot:action></x-workspace-empty>
    @else
        @foreach($courses->groupBy(fn($course) => $course->category ?: 'Learning') as $category => $group)
            <section class="workspace-section"><div class="workspace-section__heading"><h2>{{ $category }}</h2><span>{{ $group->count() }} {{ Str::plural('course',$group->count()) }}</span></div>
                <div class="course-library-grid">@foreach($group as $course)<article class="course-library-card {{ $course->member_has_access ? '' : 'is-locked' }}"><div><span>{{ $course->member_has_access ? 'Included' : 'Locked' }}</span><h3>{{ $course->title }}</h3><p>{{ Str::limit($course->description ?: 'Course details will be published by the learning team.', 150) }}</p></div><footer><span>{{ $course->modules_count }} {{ Str::plural('module',$course->modules_count) }}</span>@if($course->member_has_access)<a href="{{ route('learn.course',$course) }}">{{ $course->member_progress ? 'Resume' : 'Open course' }}</a>@else<a href="{{ route('courses.index') }}">View access options</a>@endif</footer>@if($course->member_progress)<div class="course-progress"><i style="width:{{ $course->member_progress->percentage ?? 0 }}%"></i></div>@endif</article>@endforeach</div>
            </section>
        @endforeach
    @endif
</div></div>
@endsection
