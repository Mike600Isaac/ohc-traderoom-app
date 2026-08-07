<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\DailyGamePlan;
use App\Models\GlossaryTerm;
use App\Models\MarketReport;
use Illuminate\Http\Request;

class ResearchController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $reports = MarketReport::query()->where('status', 'published')->whereNotNull('published_at')
            ->when($search, fn ($query) => $query->where(fn ($inner) => $inner->where('title', 'like', "%{$search}%")->orWhere('summary', 'like', "%{$search}%")->orWhere('body', 'like', "%{$search}%")))
            ->latest('published_at')->paginate(9)->withQueryString();
        $plans = DailyGamePlan::query()->whereNotNull('published_at')
            ->when($search, fn ($query) => $query->where(fn ($inner) => $inner->where('title', 'like', "%{$search}%")->orWhere('market', 'like', "%{$search}%")))
            ->latest('trading_date')->limit(12)->get();
        $terms = GlossaryTerm::query()->where('status', 'published')
            ->when($search, fn ($query) => $query->where(fn ($inner) => $inner->where('term', 'like', "%{$search}%")->orWhere('definition', 'like', "%{$search}%")))
            ->orderBy('term')->limit(40)->get();
        $hasPremiumPlans = in_array($request->user()->current_path, ['Trader', 'Ultimate'], true) || $request->user()->loadMissing('entitlements')->hasActiveBundle('Trader') || $request->user()->hasActiveBundle('Ultimate');
        return view('member.research.index', compact('reports', 'plans', 'terms', 'search', 'hasPremiumPlans'));
    }

    public function report(MarketReport $report)
    {
        abort_unless($report->status === 'published' && $report->published_at, 404);
        return view('member.research.report', compact('report'));
    }
}
