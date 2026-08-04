<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\DailyGamePlan;
use App\Models\MarketReport;
use Illuminate\Http\Request;

class MarketWorkspaceController extends Controller
{
    public function index(Request $request)
    {
        return view('member.markets.index', [
            'gamePlan' => DailyGamePlan::query()->whereNotNull('published_at')->latest('published_at')->first(),
            'reports' => MarketReport::query()->where('status', 'published')->whereNotNull('published_at')->latest('published_at')->limit(4)->get(),
            'timezone' => $request->user()->timezone ?: 'Africa/Lagos',
        ]);
    }
}
