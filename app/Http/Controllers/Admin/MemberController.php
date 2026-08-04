<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberEntitlement;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        AdminAccess::require($request->user(), 'members.view');

        $members = User::query()
            ->withCount('entitlements')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($q) => $q->where('email', 'like', $term)
                    ->orWhere('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function edit(Request $request, User $member)
    {
        AdminAccess::require($request->user(), 'members.view');
        $member->load(['entitlements' => fn ($query) => $query->latest()]);

        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        AdminAccess::require($request->user(), 'members.manage');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($member->id)],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Suspended'])],
            'current_path' => ['required', Rule::in(['Free', 'Foundation', 'Trader', 'Investor', 'Ultimate'])],
            'role' => ['required', Rule::in(AdminAccess::ROLES)],
            'timezone' => ['required', 'timezone'],
            'email_verified' => ['nullable', 'boolean'],
        ]);

        if ($validated['role'] !== $member->role) {
            AdminAccess::require($request->user(), 'roles.manage');
            if ($member->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
                return back()->withErrors(['role' => 'The final Super Admin cannot be demoted.'])->withInput();
            }
        }

        if ($member->is($request->user()) && $validated['status'] !== 'Active') {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.'])->withInput();
        }

        $member->fill(collect($validated)->except('email_verified')->all());
        $member->email_verified_at = $request->boolean('email_verified')
            ? ($member->email_verified_at ?: now())
            : null;
        $member->save();

        AdminAudit::record($request, 'member.updated', $member, "Updated {$member->email}", AdminAudit::changes($member));

        return back()->with('status', 'Member account updated.');
    }

    public function storeEntitlement(Request $request, User $member)
    {
        AdminAccess::require($request->user(), 'entitlements.manage');

        $validated = $request->validate([
            'offer_name' => ['required', 'string', 'max:255'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'offer_type' => ['nullable', 'string', 'max:100'],
            'external_reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'started_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ]);

        $entitlement = $member->entitlements()->create($validated);
        AdminAudit::record($request, 'entitlement.created', $entitlement, "Granted {$validated['offer_name']} to {$member->email}", $validated);

        return back()->with('status', 'Entitlement added.');
    }

    public function updateEntitlement(Request $request, User $member, MemberEntitlement $entitlement)
    {
        AdminAccess::require($request->user(), 'entitlements.manage');
        abort_unless($entitlement->user_id === $member->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'expires_at' => ['nullable', 'date'],
        ]);
        $entitlement->update($validated);
        AdminAudit::record($request, 'entitlement.updated', $entitlement, "Updated entitlement for {$member->email}", AdminAudit::changes($entitlement));

        return back()->with('status', 'Entitlement updated.');
    }
}
