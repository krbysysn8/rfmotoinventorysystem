<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $primaryKey = 'variation_id';

    protected $fillable = [
        'product_id', 'variation_name', 'sku', 'barcode',
        'unit_price', 'cost_price', 'stock_qty', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_qty'  => 'integer',
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
