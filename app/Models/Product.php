<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'sku', 'barcode', 'product_name', 'description',
        'category_id', 'supplier_id', 'brand',
        'unit_price', 'cost_price', 'stock_qty',
        'reorder_level', 'image_url', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'product_id');
    }

    // Accessor — computed stock status
    public function getStockStatusAttribute(): string
    {
        if ($this->stock_qty === 0) return 'out_of_stock';
        if ($this->stock_qty <= $this->reorder_level) return 'low_stock';
        return 'in_stock';
    }
}
