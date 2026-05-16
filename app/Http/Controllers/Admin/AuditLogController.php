<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AdminLog::with(['admin.pengguna'])
            ->when($request->get('admin_id'), fn($q) => $q->where('admin_id', $request->get('admin_id')))
            ->when($request->get('aksi'), fn($q) => $q->where('aksi', $request->get('aksi')))
            ->when($request->get('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->get('start_date')))
            ->when($request->get('end_date'), fn($q) => $q->whereDate('created_at', '<=', $request->get('end_date')))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $admins = \App\Models\Admin::with('pengguna')->orderBy('admin_id')->get();

        return view('admin.audit-log.index', [
            'logs' => $logs,
            'admins' => $admins,
        ]);
    }
}
