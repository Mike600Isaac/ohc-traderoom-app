<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationCenterController extends Controller
{
    private const TYPES = ['morning_briefing', 'live_sessions', 'learning', 'community'];

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);
        $saved = NotificationPreference::where('user_id', $request->user()->id)->get()->keyBy('type');
        $preferences = collect(self::TYPES)->map(fn ($type) => $saved->get($type) ?: new NotificationPreference(['type' => $type, 'in_app' => true, 'email' => true, 'push' => false]));
        return view('member.notifications.index', compact('notifications', 'preferences'));
    }

    public function read(Request $request, string $notification)
    {
        $record = $request->user()->notifications()->findOrFail($notification);
        $record->markAsRead();
        $url = $record->data['url'] ?? null;
        return $url ? redirect($url) : back();
    }

    public function readAll(Request $request) { $request->user()->unreadNotifications->markAsRead(); return back()->with('status', 'Notifications marked as read.'); }

    public function preferences(Request $request)
    {
        $data = $request->validate(['preferences' => ['array'], 'quiet_start' => ['nullable', 'date_format:H:i'], 'quiet_end' => ['nullable', 'date_format:H:i']]);
        foreach (self::TYPES as $type) {
            $channels = $data['preferences'][$type] ?? [];
            NotificationPreference::updateOrCreate(['user_id' => $request->user()->id, 'type' => $type], ['in_app' => in_array('in_app', $channels, true), 'email' => in_array('email', $channels, true), 'push' => in_array('push', $channels, true), 'quiet_start' => $data['quiet_start'] ?? null, 'quiet_end' => $data['quiet_end'] ?? null]);
        }
        return back()->with('status', 'Notification preferences saved.');
    }
}
