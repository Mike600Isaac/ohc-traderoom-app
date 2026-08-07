@extends('layouts.member')
@section('title', $lesson->title)
@section('content')
<div class="workspace-canvas lesson-canvas"><div class="ohc-dashboard-container">
    <div class="lesson-shell">
        <aside class="lesson-sidebar"><a href="{{ route('learn.course',$course) }}">&larr; {{ $course->title }}</a>@foreach($course->modules as $module)<h2>{{ $module->title }}</h2><ol>@foreach($module->lessons as $item)<li class="{{ $item->id === $lesson->id ? 'is-active' : '' }}"><a href="{{ route('learn.lesson',[$course,$item]) }}">{{ $item->title }}</a></li>@endforeach</ol>@endforeach</aside>
        <article class="lesson-player"><header><p>Lesson</p><h1>{{ $lesson->title }}</h1>@if($lesson->duration_minutes)<span>{{ $lesson->duration_minutes }} minutes</span>@endif</header>
            <div class="lesson-media">@if($lesson->video_url)<iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" allowfullscreen></iframe>@else<div><span>Video not published</span><p>The lesson text and resources remain available below.</p></div>@endif</div>
            <div class="lesson-content">{!! nl2br(e($lesson->body ?: 'Lesson notes have not been published yet.')) !!}</div>
            @if($lesson->document_path)<a class="workspace-button is-secondary" href="{{ \Illuminate\Support\Facades\Storage::url($lesson->document_path) }}" target="_blank" rel="noopener">Open lesson resource</a>@endif
            <form method="POST" action="{{ route('learn.progress',$lesson) }}" class="lesson-notes">@csrf @method('PATCH')<label>Personal notes<textarea name="notes" rows="5">{{ old('notes',$progress->notes) }}</textarea></label><label class="check-row"><input type="checkbox" name="bookmarked" value="1" {{ $progress->bookmarked ? 'checked' : '' }}> Bookmark this lesson</label><div><button class="workspace-button is-secondary" type="submit">Save notes</button><button class="workspace-button" type="submit" name="complete" value="1">{{ $progress->completed_at ? 'Completed' : 'Mark complete' }}</button></div></form>
        </article>
    </div>
</div></div>
@endsection
