<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subcategory extends Model
{
    protected $primaryKey = 'subcategory_id';
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'subcategory_name',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}