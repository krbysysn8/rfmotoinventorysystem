<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $primaryKey = 'movement_id';

    // Disable Laravel's default updated_at (table only has created_at)
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'variation_id',
        'movement_type',
        'quantity',
        'qty_before',
        'qty_after',
        'reference_no',
        'reference_type',
        'reference_id',
        'notes',
        'movement_reason',
        'performed_by',   // used by SalesController & BarcodeController
        'created_by',     // alias — kept for compatibility
        'movement_date',
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'qty_before'    => 'integer',
        'qty_after'     => 'integer',
        'movement_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by', 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
