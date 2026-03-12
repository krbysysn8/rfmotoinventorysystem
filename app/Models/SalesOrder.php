<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $primaryKey = 'order_id';

    // sales_orders table has no updated_at column
    const UPDATED_AT = null;

    protected $fillable = [
        'order_number', 'customer_name',
        'subtotal', 'discount', 'total_amount',
        'payment_method', 'status', 'order_date', 'served_by',
    ];

    protected $casts = [
        'order_date'   => 'datetime',
        'total_amount' => 'decimal:2',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'order_id', 'order_id');
    }

    public function servedBy()
    {
        return $this->belongsTo(User::class, 'served_by', 'user_id');
    }
}