<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use App\Models\DailyGamePlan;
use App\Models\LiveSession;
use App\Models\WeeklyGoal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->loadMissing('entitlements');
        $now = now();

        $activeEntitlements = $user->entitlements
            ->filter(fn ($entitlement) => $entitlement->status === 'Active'
                && (is_null($entitlement->expires_at) || $entitlement->expires_at->isFuture()))
            ->values();

        $pathNames = array_keys(config('ohc_access.bundles', []));
        $currentPath = in_array($user->current_path, $pathNames, true)
            ? $user->current_path
            : null;

        $accessEndsAt = $activeEntitlements
            ->pluck('expires_at')
            ->filter()
            ->sort()
            ->first();

        $courseProgress = CourseProgress::query()
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity_at')
            ->first();

        $todayFocus = $courseProgress ? [
            'title' => $courseProgress->current_item_title ?: config("ohc_access.courses.{$courseProgress->course_key}.title", 'Continue learning'),
            'meta' => config("ohc_access.courses.{$courseProgress->course_key}.title", 'Course activity'),
            'progress' => $courseProgress->percentage,
            'url' => $courseProgress->resume_url ?: route('courses.index'),
        ] : null;

        $weeklyGoal = WeeklyGoal::query()
            ->where('user_id', $user->id)
            ->whereDate('week_starts_on', $now->copy()->startOfWeek()->toDateString())
            ->first();

        $nextSession = LiveSession::query()
            ->whereNotNull('published_at')
            ->where('starts_at', '>=', $now)
            ->orderBy('starts_at')
            ->first();

        $gamePlan = DailyGamePlan::query()
            ->whereNotNull('published_at')
            ->whereDate('trading_date', $now->toDateString())
            ->latest('published_at')
            ->first();

        $attention = collect();

        if (! $user->hasVerifiedEmail()) {
            $attention->push([
                'title' => 'Verify your email address',
                'copy' => 'Verification protects your account and restores full member access.',
                'url' => route('verification.notice'),
                'action' => 'Verify email',
            ]);
        }

        if (! $currentPath && $activeEntitlements->isEmpty()) {
            $attention->push([
                'title' => 'Choose your learning path',
                'copy' => 'Your account does not yet have an active path or standalone course.',
                'url' => route('courses.index'),
                'action' => 'Explore courses',
            ]);
        }

        if (! $user->avatar_url) {
            $attention->push([
                'title' => 'Complete your member profile',
                'copy' => 'Add a profile photo and review your account details.',
                'url' => route('profile.edit'),
                'action' => 'Open profile',
            ]);
        }

        return view('dashboard', [
            'user' => $user,
            'activeEntitlements' => $activeEntitlements,
            'currentPath' => $currentPath,
            'accessEndsAt' => $accessEndsAt,
            'todayFocus' => $todayFocus,
            'weeklyGoal' => $weeklyGoal,
            'nextSession' => $nextSession,
            'gamePlan' => $gamePlan,
            'attention' => $attention->take(3),
        ]);
    }
}