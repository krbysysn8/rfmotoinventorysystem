<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    protected $table = 'return_requests';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'platform',
        'courier',
        'item_status',
        'bad_reason',
        'quantity',
        'return_date',
        'notes',
        'logged_by',
    ];

    protected $casts = [
        'return_date' => 'date:Y-m-d',
        'quantity'    => 'integer',
        'product_id'  => 'integer',
    ];

    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by', 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}