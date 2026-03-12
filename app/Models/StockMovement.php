<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $primaryKey = 'movement_id';

    protected $fillable = [
        'product_id', 'variation_id', 'movement_type',
        'quantity', 'reference_type', 'reference_id',
        'notes', 'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
