<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'serial',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'serial' => 'integer',
    ];

    /**
     * Get the products for this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Get the active products count for this category
     */
    public function getProductsCountAttribute()
    {
        return $this->products()
            ->where('status', 'Active')
            ->where('is_show_frontend', true)
            ->count();
    }
}