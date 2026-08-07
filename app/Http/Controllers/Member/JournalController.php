<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Trade::where('user_id', $request->user()->id)->latest('traded_at');
        if ($request->filled('instrument')) $query->where('instrument', strtoupper((string) $request->string('instrument')));
        if ($request->filled('outcome')) $query->where('outcome', (string) $request->string('outcome'));
        $trades = $query->paginate(15)->withQueryString();
        $all = Trade::where('user_id', $request->user()->id)->get();
        $closed = $all->whereIn('outcome', ['win', 'loss', 'breakeven']);
        $stats = [
            'count' => $all->count(),
            'win_rate' => $closed->count() ? round($closed->where('outcome', 'win')->count() / $closed->count() * 100) : null,
            'average_r' => $all->whereNotNull('r_multiple')->count() ? round((float) $all->whereNotNull('r_multiple')->avg('r_multiple'), 2) : null,
            'instrument' => $all->countBy('instrument')->sortDesc()->keys()->first(),
        ];
        return view('member.journal.index', compact('trades', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'instrument' => ['required', 'string', 'max:32'], 'direction' => ['required', 'in:long,short'],
            'entry_price' => ['nullable', 'numeric', 'min:0'], 'stop_price' => ['nullable', 'numeric', 'min:0'], 'target_price' => ['nullable', 'numeric', 'min:0'],
            'risk_percent' => ['nullable', 'numeric', 'between:0,100'], 'emotion' => ['nullable', 'string', 'max:32'], 'confidence' => ['nullable', 'integer', 'between:1,5'],
            'outcome' => ['required', 'in:open,win,loss,breakeven'], 'r_multiple' => ['nullable', 'numeric', 'between:-100,100'],
            'lessons' => ['nullable', 'string', 'max:10000'], 'traded_at' => ['required', 'date'], 'screenshot' => ['nullable', 'image', 'max:5120'],
        ]);
        $data['user_id'] = $request->user()->id;
        $data['instrument'] = strtoupper(trim($data['instrument']));
        unset($data['screenshot']);
        if ($request->hasFile('screenshot')) $data['screenshot_path'] = $request->file('screenshot')->store('trade-journal', 'public');
        Trade::create($data);
        return back()->with('status', 'Trade recorded.');
    }

    public function destroy(Request $request, Trade $trade)
    {
        abort_unless($trade->user_id === $request->user()->id, 404);
        if ($trade->screenshot_path) Storage::disk('public')->delete($trade->screenshot_path);
        $trade->delete();
        return back()->with('status', 'Trade removed.');
    }
}
