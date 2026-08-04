<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\AnalyticsEvent;
use App\Models\CourseProgress;
use App\Models\DailyGamePlan;
use App\Models\LiveSession;
use App\Models\MemberEntitlement;
use App\Models\PaymentTransaction;
use App\Models\User;

class AdminAnalyticsService
{
    public function dashboard(): array
    {
        $activeMembers = User::query()->where('status', 'Active')->where('role', 'member');
        $atRisk = (clone $activeMembers)->where(function ($query) {
            $query->whereNull('last_login_at')
                ->orWhere('last_login_at', '<', now()->subDays(30));
        })->count();

        $paid = PaymentTransaction::query()->whereIn('status', ['success', 'successful', 'paid']);

        return [
            'members_total' => User::query()->where('role', 'member')->count(),
            'members_active' => (clone $activeMembers)->count(),
            'members_unverified' => User::query()->where('role', 'member')->whereNull('email_verified_at')->count(),
            'members_at_risk' => $atRisk,
            'active_entitlements' => MemberEntitlement::query()->where('status', 'Active')
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count(),
            'learning_active_7d' => CourseProgress::query()->where('last_activity_at', '>=', now()->subDays(7))->distinct('user_id')->count('user_id'),
            'upcoming_sessions' => LiveSession::query()->where('starts_at', '>=', now())->count(),
            'game_plans_draft' => DailyGamePlan::query()->where('status', 'draft')->count(),
            'revenue_minor_units' => (clone $paid)->sum('amount'),
            'payments_successful' => (clone $paid)->count(),
            'recent_audits' => AdminAuditLog::query()->with('actor')->latest('created_at')->limit(8)->get(),
        ];
    }

    public function intelligence(int $days = 30): array
    {
        $days = max(7, min(365, $days));
        $start = now()->subDays($days - 1)->startOfDay();
        $activeMembers = User::query()->where('status', 'Active')->where('role', 'member');
        $total = (clone $activeMembers)->count();

        $activity = AnalyticsEvent::query()->where('occurred_at', '>=', $start)
            ->selectRaw('DATE(occurred_at) as day, COUNT(*) as total')
            ->groupBy('day')->orderBy('day')->get();

        $popularLessons = AnalyticsEvent::query()
            ->where('event_name', 'lesson.viewed')->where('subject_type', 'lesson')
            ->where('occurred_at', '>=', $start)
            ->selectRaw('subject_id, COUNT(*) as views, COUNT(DISTINCT user_id) as members')
            ->groupBy('subject_id')->orderByDesc('views')->limit(10)->get();

        $revenue = PaymentTransaction::query()->whereIn('status', ['success', 'successful', 'paid'])
            ->where('paid_at', '>=', $start)
            ->selectRaw('currency, SUM(amount) as amount, COUNT(*) as transactions')
            ->groupBy('currency')->get();

        return [
            'days' => $days,
            'activity' => $activity,
            'activity_max' => max(1, (int) $activity->max('total')),
            'popular_lessons' => $popularLessons,
            'lesson_views' => AnalyticsEvent::where('event_name', 'lesson.viewed')->where('occurred_at', '>=', $start)->count(),
            'lesson_completions' => AnalyticsEvent::where('event_name', 'lesson.completed')->where('occurred_at', '>=', $start)->count(),
            'members_active_7d' => (clone $activeMembers)->where('last_login_at', '>=', now()->subDays(7))->count(),
            'members_active_30d' => (clone $activeMembers)->where('last_login_at', '>=', now()->subDays(30))->count(),
            'members_total' => $total,
            'retention_30d' => $total ? round(((clone $activeMembers)->where('last_login_at', '>=', now()->subDays(30))->count() / $total) * 100, 1) : null,
            'at_risk' => (clone $activeMembers)->where(fn ($query) => $query->whereNull('last_login_at')->orWhere('last_login_at', '<', now()->subDays(30)))->orderBy('last_login_at')->limit(20)->get(),
            'revenue' => $revenue,
        ];
    }
}
