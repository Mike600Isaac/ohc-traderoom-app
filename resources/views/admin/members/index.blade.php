@extends('layouts.admin')
@section('title', 'Members')
@section('heading', 'Member Management')
@section('content')
<form method="GET" class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(220px,1fr)_180px_180px_auto]">
    <input name="q" value="{{ request('q') }}" placeholder="Search name or email" class="rounded-lg border-slate-300 text-sm">
    <select name="role" class="rounded-lg border-slate-300 text-sm"><option value="">All roles</option>@foreach(\App\Support\AdminAccess::ROLES as $role)<option value="{{ $role }}" @selected(request('role')===$role)>{{ ucwords(str_replace('_',' ',$role)) }}</option>@endforeach</select>
    <select name="status" class="rounded-lg border-slate-300 text-sm"><option value="">All statuses</option>@foreach(['Active','Inactive','Suspended'] as $status)<option @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
    <button class="rounded-lg bg-[#10203d] px-5 py-3 text-sm font-black text-white">Filter</button>
</form>
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-4">Member</th><th class="px-5 py-4">Role</th><th class="px-5 py-4">Path</th><th class="px-5 py-4">Status</th><th class="px-5 py-4">Entitlements</th><th class="px-5 py-4">Last login</th><th class="px-5 py-4"></th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($members as $member)<tr><td class="px-5 py-4"><p class="font-black text-[#10203d]">{{ $member->first_name }} {{ $member->last_name }}</p><p class="text-xs text-slate-500">{{ $member->email }}</p></td><td class="px-5 py-4">{{ ucwords(str_replace('_',' ',$member->role)) }}</td><td class="px-5 py-4">{{ $member->current_path }}</td><td class="px-5 py-4"><span class="rounded-full px-2 py-1 text-xs font-black {{ $member->status==='Active'?'bg-emerald-50 text-emerald-700':'bg-amber-50 text-amber-700' }}">{{ $member->status }}</span></td><td class="px-5 py-4">{{ $member->entitlements_count }}</td><td class="px-5 py-4 text-xs">{{ $member->last_login_at?->diffForHumans() ?? 'Never' }}</td><td class="px-5 py-4"><a href="{{ route('admin.members.edit',$member) }}" class="font-black text-teal-700">Manage →</a></td></tr>@empty<tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">No matching members.</td></tr>@endforelse</tbody></table></div>
</div>
<div class="mt-5">{{ $members->links() }}</div>
@endsection
