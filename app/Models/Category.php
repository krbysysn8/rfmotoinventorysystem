<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $primaryKey = 'category_id';

    protected $fillable = [
    'category_name',
    'description',
    'icon',
    'color_hex',
];

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class, 'category_id', 'category_id')
                    ->orderBy('subcategory_name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}