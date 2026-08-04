<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use App\Models\SessionReminder;
use Illuminate\Http\Request;

class TradeRoomController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('entitlements');
        $sessions = LiveSession::query()->whereNotNull('published_at')->where('starts_at', '>=', now()->subHours(4))->orderBy('starts_at')->get();
        return view('member.trade-room.index', [
            'hasAccess' => $user->hasCourseAccess('live_room'),
            'nextSession' => $sessions->first(fn ($session) => $session->starts_at->isFuture() || ($session->ends_at && $session->ends_at->isFuture())),
            'upcoming' => $sessions->filter(fn ($session) => $session->starts_at->isFuture())->take(8),
            'replays' => LiveSession::query()->whereNotNull('published_at')->whereNotNull('replay_url')->latest('starts_at')->limit(8)->get(),
            'reminderIds' => SessionReminder::where('user_id', $user->id)->pluck('live_session_id'),
        ]);
    }

    public function reminder(Request $request, LiveSession $session)
    {
        abort_unless($session->published_at && $session->starts_at->isFuture(), 404);
        $existing = SessionReminder::where('user_id', $request->user()->id)->where('live_session_id', $session->id)->first();
        if ($existing) {
            $existing->delete();
            return back()->with('status', 'Reminder removed.');
        }
        SessionReminder::create(['user_id' => $request->user()->id, 'live_session_id' => $session->id, 'remind_at' => $session->starts_at->copy()->subMinutes(30)]);
        return back()->with('status', 'Reminder set for 30 minutes before the session.');
    }
}
