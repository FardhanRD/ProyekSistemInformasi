<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;

class AdminLogger
{
    public function logActivity(?int $adminId, string $module, string $action, ?string $description = null, array $metadata = []): void
    {
        AdminActivityLog::create([
            'admin_id' => $adminId,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => (string) request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    public function logAudit(?int $adminId, string $action, Model $model, ?array $before = null, ?array $after = null): void
    {
        AuditTrail::create([
            'table_name' => $model->getTable(),
            'row_id' => (int) $model->getKey(),
            'action' => $action,
            'before_data' => $before,
            'after_data' => $after,
            'changed_by' => $adminId,
        ]);
    }
}
