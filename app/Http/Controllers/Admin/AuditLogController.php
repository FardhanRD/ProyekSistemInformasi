<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function activity(Request $request)
    {
        $query = AdminActivityLog::with('admin')->latest();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        return response()->json($query->paginate(30));
    }

    public function trails(Request $request)
    {
        $query = AuditTrail::with('changedBy')->latest();

        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        if ($request->filled('row_id')) {
            $query->where('row_id', $request->row_id);
        }

        return response()->json($query->paginate(30));
    }
}
