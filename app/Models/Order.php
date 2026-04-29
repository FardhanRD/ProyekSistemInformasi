<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'midtrans_order_id',
        'payment_url',
        'invoice_number',
        'verified_paid_at',
        'stock_reduced_at',
        'courier_service',
        'tracking_number',
        'shipping_status',
        'shipping_cost',
        'transaction_time',
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'verified_paid_at' => 'datetime',
        'stock_reduced_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if order is paid
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if order is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}