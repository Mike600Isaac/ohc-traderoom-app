<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\CommunityChannel;
use App\Models\CommunityPost;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $channels = CommunityChannel::where('status', 'published')->withCount('posts')->get()->map(function ($channel) use ($request) {
            $channel->member_has_access = $this->allowed($request, $channel);
            return $channel;
        });
        return view('member.community.index', compact('channels'));
    }

    public function channel(Request $request, CommunityChannel $channel)
    {
        abort_unless($channel->status === 'published', 404);
        abort_unless($this->allowed($request, $channel), 403, 'This channel is not included in your current Path.');
        $posts = CommunityPost::where('channel_id', $channel->id)->whereNull('parent_id')->with(['author', 'replies'])->latest()->paginate(20);
        return view('member.community.channel', compact('channel', 'posts'));
    }

    public function store(Request $request, CommunityChannel $channel)
    {
        abort_unless($channel->status === 'published' && $this->allowed($request, $channel), 403);
        $data = $request->validate(['body' => ['required', 'string', 'max:10000'], 'parent_id' => ['nullable', 'integer'], 'attachment_url' => ['nullable', 'url', 'max:2048']]);
        if (! empty($data['parent_id'])) abort_unless(CommunityPost::where('id', $data['parent_id'])->where('channel_id', $channel->id)->exists(), 422);
        CommunityPost::create($data + ['channel_id' => $channel->id, 'user_id' => $request->user()->id]);
        return back()->with('status', $data['parent_id'] ?? null ? 'Reply posted.' : 'Post published.');
    }

    private function allowed(Request $request, CommunityChannel $channel): bool
    {
        return ! $channel->required_path || $request->user()->loadMissing('entitlements')->hasActiveBundle($channel->required_path);
    }
}
