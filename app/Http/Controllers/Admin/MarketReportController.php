<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketReport;
use App\Services\PublishingService;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketReportController extends Controller
{
    public function index(Request $request)
    {
        AdminAccess::require($request->user(), 'publishing.view');
        $items = MarketReport::with('author')->latest()->paginate(20);
        return view('admin.publishing.reports', compact('items'));
    }

    public function create(Request $request)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        return view('admin.publishing.report-form', ['item' => new MarketReport]);
    }

    public function store(Request $request, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        $data = $this->validated($request);
        $item = new MarketReport(collect($data)->except(['status', 'scheduled_for'])->all());
        $item->author_user_id = $request->user()->id;
        $item->slug = $this->uniqueSlug($data['title']);
        $publisher->apply($item, $data['status'], $data['scheduled_for'] ?? null);
        $item->save();
        AdminAudit::record($request, 'market_report.created', $item, "Created market report: {$item->title}", $item->toArray());
        return redirect()->route('admin.reports.edit', $item)->with('status', 'Market report saved.');
    }

    public function edit(Request $request, MarketReport $report)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        return view('admin.publishing.report-form', ['item' => $report]);
    }

    public function update(Request $request, MarketReport $report, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        $data = $this->validated($request);
        $report->fill(collect($data)->except(['status', 'scheduled_for'])->all());
        $publisher->apply($report, $data['status'], $data['scheduled_for'] ?? null);
        $report->save();
        AdminAudit::record($request, 'market_report.updated', $report, "Updated market report: {$report->title}", AdminAudit::changes($report));
        return back()->with('status', 'Market report updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'scheduled', 'published'])],
            'scheduled_for' => ['nullable', 'required_if:status,scheduled', 'date'],
        ]);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'report';
        $slug = $base;
        $counter = 2;
        while (MarketReport::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }
        return $slug;
    }
}
