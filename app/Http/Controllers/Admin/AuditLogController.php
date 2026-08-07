<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Support\AdminAccess;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        AdminAccess::require($request->user(), 'audit.view');
        $logs = AdminAuditLog::with('actor')
            ->when($request->filled('action'), fn ($query) => $query->where('action', 'like', '%'.$request->string('action').'%'))
            ->latest('created_at')->paginate(30)->withQueryString();
        return view('admin.audit.index', compact('logs'));
    }
}
