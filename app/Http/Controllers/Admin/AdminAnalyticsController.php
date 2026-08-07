<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;
use App\Support\AdminAccess;
use Illuminate\Http\Request;

class AdminAnalyticsController extends Controller
{
    public function __invoke(Request $request, AdminAnalyticsService $analytics)
    {
        AdminAccess::require($request->user(), 'analytics.view');
        $days = (int) $request->integer('days', 30);
        return view('admin.analytics.index', ['data' => $analytics->intelligence($days)]);
    }
}
