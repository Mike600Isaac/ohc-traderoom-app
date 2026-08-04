<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityChannel;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CommunityChannelAdminController extends Controller
{
    public function index(Request $request)
    {
        AdminAccess::require($request->user(), 'community.view');
        return view('admin.community.index', ['channels' => CommunityChannel::orderBy('name')->paginate(20)]);
    }

    public function store(Request $request)
    {
        AdminAccess::require($request->user(), 'community.manage');
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $channel = CommunityChannel::create($data);
        AdminAudit::record($request, 'channel.created', $channel, "Created community channel: {$channel->name}", $data);
        return back()->with('status', 'Community channel created.');
    }

    public function update(Request $request, CommunityChannel $channel)
    {
        AdminAccess::require($request->user(), 'community.manage');
        $channel->update($this->validated($request));
        AdminAudit::record($request, 'channel.updated', $channel, "Updated community channel: {$channel->name}", AdminAudit::changes($channel));
        return back()->with('status', 'Community channel updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'required_path' => ['nullable', Rule::in(['Foundation', 'Trader', 'Investor', 'Ultimate'])],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'channel';
        $slug = $base;
        $counter = 2;
        while (CommunityChannel::where('slug', $slug)->exists()) $slug = $base.'-'.$counter++;
        return $slug;
    }
}
