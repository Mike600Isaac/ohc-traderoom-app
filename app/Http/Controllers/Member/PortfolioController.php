<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Holding;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $portfolios = Portfolio::where('user_id', $request->user()->id)->with('holdings')->get();
        $portfolio = $portfolios->firstWhere('id', (int) $request->query('portfolio')) ?: $portfolios->first();
        $holdings = $portfolio?->holdings ?? collect();
        $totalCost = $holdings->sum(fn ($holding) => (float) $holding->quantity * (float) $holding->average_cost);
        $rows = $holdings->map(function ($holding) use ($totalCost) {
            $cost = (float) $holding->quantity * (float) $holding->average_cost;
            $holding->recorded_cost = $cost;
            $holding->recorded_weight = $totalCost > 0 ? round($cost / $totalCost * 100, 2) : 0;
            $holding->recorded_drift = $holding->target_weight !== null ? round($holding->recorded_weight - (float) $holding->target_weight, 2) : null;
            return $holding;
        });
        $allocation = $rows->groupBy('asset_class')->map(fn ($group) => round($group->sum('recorded_cost'), 2));
        return view('member.portfolio.index', compact('portfolios', 'portfolio', 'rows', 'allocation', 'totalCost'));
    }

    public function storePortfolio(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'benchmark_symbol' => ['required', 'string', 'max:24'], 'currency' => ['required', 'string', 'size:3']]);
        $data['user_id'] = $request->user()->id;
        $data['benchmark_symbol'] = strtoupper($data['benchmark_symbol']);
        $data['currency'] = strtoupper($data['currency']);
        Portfolio::create($data);
        return back()->with('status', 'Portfolio created.');
    }

    public function storeHolding(Request $request, Portfolio $portfolio)
    {
        $this->owns($request, $portfolio);
        $data = $request->validate(['symbol' => ['required', 'string', 'max:32'], 'name' => ['nullable', 'string', 'max:100'], 'asset_class' => ['required', 'string', 'max:40'], 'quantity' => ['required', 'numeric', 'gt:0'], 'average_cost' => ['required', 'numeric', 'min:0'], 'target_weight' => ['nullable', 'numeric', 'between:0,100']]);
        $data['symbol'] = strtoupper(trim($data['symbol']));
        $portfolio->holdings()->updateOrCreate(['symbol' => $data['symbol']], $data);
        return back()->with('status', 'Holding saved.');
    }

    public function destroyHolding(Request $request, Portfolio $portfolio, Holding $holding)
    {
        $this->owns($request, $portfolio);
        abort_unless($holding->portfolio_id === $portfolio->id, 404);
        $holding->delete();
        return back()->with('status', 'Holding removed.');
    }

    private function owns(Request $request, Portfolio $portfolio): void { abort_unless($portfolio->user_id === $request->user()->id, 404); }
}
