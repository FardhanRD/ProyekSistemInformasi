<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\AdminLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AdminActivityObserver
{
    public function created(Model $model): void
    {
        $this->storeLog('created', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->storeLog('updated', $model, $model->getOriginal(), $model->getAttributes());
    }

    public function deleted(Model $model): void
    {
        $this->storeLog('deleted', $model, $model->getOriginal(), null);
    }

    protected function storeLog(string $action, Model $model, ?array $oldData, ?array $newData): void
    {
        if (!Schema::hasTable('admin_log')) {
            return;
        }

        $user = auth()->user();
        if (!$user) {
            return;
        }

        $admin = Admin::where('pengguna_id', $user->pengguna_id)->first();
        if (!$admin) {
            return;
        }

        AdminLog::create([
            'admin_id' => $admin->admin_id,
            'aksi' => $action,
            'tabel' => $model->getTable(),
            'record_id' => $model->getKey(),
            'data_lama' => $this->sanitize($oldData),
            'data_baru' => $this->sanitize($newData),
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 300),
            'created_at' => now(),
        ]);
    }

    protected function sanitize(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        unset($data['remember_token'], $data['password'], $data['sandi']);

        foreach ($data as $key => $value) {
            if ($value instanceof \DateTimeInterface) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        return $data;
    }
}
