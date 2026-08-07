@extends('layouts.member')
@section('title', $channel->name)
@section('content')
<div class="workspace-canvas"><div class="ohc-dashboard-container channel-page">
    <x-workspace-heading eyebrow="Community channel" :title="$channel->name" :description="$channel->description ?: 'Member discussion channel.'"><x-slot:actions><a class="workspace-button is-secondary" href="{{ route('community.index') }}">All channels</a></x-slot:actions></x-workspace-heading>
    <section class="workspace-card post-composer"><form method="POST" action="{{ route('community.store',$channel) }}">@csrf<label>Share with the channel<textarea name="body" rows="4" required placeholder="Add context, your reasoning, and a clear question.">{{ old('body') }}</textarea></label><label>Optional chart or resource URL<input type="url" name="attachment_url" value="{{ old('attachment_url') }}" placeholder="https://"></label><button class="workspace-button">Publish post</button></form></section>
    <div class="community-feed">@forelse($posts as $post)<article class="community-post"><header><span>{{ strtoupper(substr($post->author->first_name ?: 'M',0,1)) }}</span><div><strong>{{ trim($post->author->first_name.' '.$post->author->last_name) ?: 'Member' }}</strong><time>{{ $post->created_at->diffForHumans() }}</time></div></header><p>{!! nl2br(e($post->body)) !!}</p>@if($post->attachment_url)<a href="{{ $post->attachment_url }}" target="_blank" rel="noopener nofollow">Open shared resource</a>@endif
        @if($post->replies->isNotEmpty())<div class="community-replies">@foreach($post->replies as $reply)<article><strong>{{ trim($reply->author->first_name.' '.$reply->author->last_name) ?: 'Member' }}</strong><time>{{ $reply->created_at->diffForHumans() }}</time><p>{!! nl2br(e($reply->body)) !!}</p></article>@endforeach</div>@endif
        <form method="POST" action="{{ route('community.store',$channel) }}" class="reply-form">@csrf<input type="hidden" name="parent_id" value="{{ $post->id }}"><input name="body" required placeholder="Write a constructive reply"><button>Reply</button></form>
    </article>@empty<x-workspace-empty title="Start the first useful discussion" copy="Share an analysis, a question, or a lesson from your process." />@endforelse</div>{{ $posts->links() }}
</div></div>
@endsection
