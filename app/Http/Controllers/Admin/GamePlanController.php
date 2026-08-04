<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyGamePlan;
use App\Services\PublishingService;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GamePlanController extends Controller
{
    public function index(Request $request)
    {
        AdminAccess::require($request->user(), 'publishing.view');
        $items = DailyGamePlan::with('author')->latest('trading_date')->paginate(20);
        return view('admin.publishing.game-plans', compact('items'));
    }

    public function create(Request $request)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        return view('admin.publishing.game-plan-form', ['item' => new DailyGamePlan]);
    }

    public function store(Request $request, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        $data = $this->validated($request);
        $item = new DailyGamePlan($this->normalise($data));
        $item->author_user_id = $request->user()->id;
        $publisher->apply($item, $data['status'], $data['scheduled_for'] ?? null);
        $item->save();
        AdminAudit::record($request, 'game_plan.created', $item, "Created game plan: {$item->title}", $item->toArray());
        return redirect()->route('admin.game-plans.edit', $item)->with('status', 'Game plan saved.');
    }

    public function edit(Request $request, DailyGamePlan $gamePlan)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        return view('admin.publishing.game-plan-form', ['item' => $gamePlan]);
    }

    public function update(Request $request, DailyGamePlan $gamePlan, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        $data = $this->validated($request);
        $gamePlan->fill($this->normalise($data));
        $publisher->apply($gamePlan, $data['status'], $data['scheduled_for'] ?? null);
        $gamePlan->save();
        AdminAudit::record($request, 'game_plan.updated', $gamePlan, "Updated game plan: {$gamePlan->title}", AdminAudit::changes($gamePlan));
        return back()->with('status', 'Game plan updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'trading_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'scheduled', 'published'])],
            'scheduled_for' => ['nullable', 'required_if:status,scheduled', 'date'],
            'market' => ['nullable', 'string', 'max:100'],
            'bias' => ['nullable', 'string', 'max:100'],
            'key_levels_text' => ['nullable', 'string'],
            'invalidation' => ['nullable', 'string'],
            'watchlist_text' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'pdf_url' => ['nullable', 'url', 'max:2048'],
            'chart_url' => ['nullable', 'url', 'max:2048'],
        ]);
    }

    private function normalise(array $data): array
    {
        $data['key_levels'] = $this->lines($data['key_levels_text'] ?? '');
        $data['watchlist'] = $this->tokens($data['watchlist_text'] ?? '');
        unset($data['key_levels_text'], $data['watchlist_text'], $data['status'], $data['scheduled_for']);
        return $data;
    }

    private function lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', $value) ?: [])));
    }

    private function tokens(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[,\r\n]+/', $value) ?: [])));
    }
}
