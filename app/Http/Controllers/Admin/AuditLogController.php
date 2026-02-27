<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $logs = $query->paginate(20);

        return view('admin.audit-logs.index', compact('logs'));
    }
}
