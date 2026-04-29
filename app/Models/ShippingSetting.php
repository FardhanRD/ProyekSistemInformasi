<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_zone',
        'courier_service',
        'cost',
        'estimated_days',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
