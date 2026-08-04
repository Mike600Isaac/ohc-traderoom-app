<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsRecorder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackMemberActivity
{
    public function __construct(private AnalyticsRecorder $analytics) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $routeName = $request->route()?->getName();
        $event = [
            'login' => 'member.logged_in',
            'dashboard' => 'workspace.home.viewed',
            'courses.index' => 'learning.catalog.viewed',
            'profile.edit' => 'account.profile.viewed',
        ][$routeName] ?? null;

        if ($event && $request->user() && $response->isSuccessful()) {
            $this->analytics->record($request->user(), $event, 'route', $routeName);
        }

        return $response;
    }
}
