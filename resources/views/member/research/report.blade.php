@extends('layouts.member')
@section('title', $report->title)
@section('content')
<div class="workspace-canvas"><div class="workspace-article">
    <a href="{{ route('research.index') }}">&larr; Research Centre</a><header><p>{{ $report->category ?: 'Market report' }}</p><h1>{{ $report->title }}</h1><span>Published {{ $report->published_at->format('j F Y, g:i A') }} @if($report->author) by {{ $report->author->first_name }} {{ $report->author->last_name }} @endif</span>@if($report->summary)<strong>{{ $report->summary }}</strong>@endif</header><article>{!! nl2br(e($report->body)) !!}</article><footer>OHC educational research only. Nothing in this report is personalised investment advice.</footer>
</div></div>
@endsection
