<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $primaryKey = 'item_id';

    // sales_order_items has no created_at / updated_at columns
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'variation_id',
        'quantity', 'unit_price', 'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}