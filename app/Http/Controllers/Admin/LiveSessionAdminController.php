<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use App\Services\PublishingService;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LiveSessionAdminController extends Controller
{
    public function index(Request $request) { AdminAccess::require($request->user(), 'sessions.view'); $items = LiveSession::with('host')->orderByDesc('starts_at')->paginate(20); return view('admin.publishing.sessions', compact('items')); }
    public function create(Request $request) { AdminAccess::require($request->user(), 'sessions.manage'); return view('admin.publishing.session-form', ['item' => new LiveSession]); }
    public function store(Request $request, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'sessions.manage'); $data = $this->validated($request); $item = new LiveSession(collect($data)->except(['status','scheduled_for'])->all()); $item->host_user_id = $request->user()->id; $publisher->apply($item, $data['status'], $data['scheduled_for'] ?? null); $item->save(); AdminAudit::record($request, 'live_session.created', $item, "Created live session: {$item->title}", $item->toArray()); return redirect()->route('admin.sessions.edit',$item)->with('status','Live session saved.');
    }
    public function edit(Request $request, LiveSession $session) { AdminAccess::require($request->user(), 'sessions.manage'); return view('admin.publishing.session-form', ['item'=>$session]); }
    public function update(Request $request, LiveSession $session, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'sessions.manage'); $data=$this->validated($request); $session->fill(collect($data)->except(['status','scheduled_for'])->all()); $publisher->apply($session,$data['status'],$data['scheduled_for']??null); $session->save(); AdminAudit::record($request,'live_session.updated',$session,"Updated live session: {$session->title}",AdminAudit::changes($session)); return back()->with('status','Live session updated.');
    }
    private function validated(Request $request): array
    {
        return $request->validate(['title'=>['required','string','max:255'],'agenda'=>['nullable','string'],'recap'=>['nullable','string'],'starts_at'=>['required','date'],'ends_at'=>['nullable','date','after:starts_at'],'join_url'=>['nullable','url','max:2048'],'replay_url'=>['nullable','url','max:2048'],'registered_count'=>['nullable','integer','min:0'],'status'=>['required',Rule::in(['draft','scheduled','published'])],'scheduled_for'=>['nullable','required_if:status,scheduled','date']]);
    }
}
