<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;

class AdminDashboardController extends Controller
{
    public function __invoke(AdminAnalyticsService $analytics)
    {
        return view('admin.dashboard', ['metrics' => $analytics->dashboard()]);
    }
}
