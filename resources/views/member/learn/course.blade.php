@extends('layouts.member')
@section('title', $course->title)
@section('content')
<div class="workspace-canvas"><div class="ohc-dashboard-container">
    <x-workspace-heading eyebrow="Learning Workspace" :title="$course->title" :description="$course->description ?: 'Published OHC course curriculum.'"><x-slot:actions><a class="workspace-button is-secondary" href="{{ route('learn.index') }}">All courses</a></x-slot:actions></x-workspace-heading>
    <div class="curriculum-list">@forelse($course->modules as $module)<section class="workspace-card curriculum-module"><div class="workspace-card__heading"><div><p>Module {{ $loop->iteration }}</p><h2>{{ $module->title }}</h2></div><span>{{ $module->lessons->count() }} lessons</span></div><ol>@foreach($module->lessons as $lesson)<li class="{{ $completed->contains($lesson->id) ? 'is-complete' : '' }}"><span>{{ $completed->contains($lesson->id) ? '✓' : str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</span><div><strong>{{ $lesson->title }}</strong><small>{{ $lesson->duration_minutes ? $lesson->duration_minutes.' min' : 'Duration not set' }}</small></div><a href="{{ route('learn.lesson',[$course,$lesson]) }}">Open</a></li>@endforeach</ol></section>@empty<x-workspace-empty title="Curriculum pending" copy="This course is published, but no lessons have been released yet." />@endforelse</div>
</div></div>
@endsection
