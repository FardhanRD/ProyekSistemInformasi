<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_name',
        'row_id',
        'action',
        'before_data',
        'after_data',
        'changed_by',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
    ];

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
