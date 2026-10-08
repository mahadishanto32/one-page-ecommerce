<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributeOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'attribute_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductAttributeVariant::class, 'product_attribute_option_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductAttributeOptionImage::class, 'product_attribute_option_id');
    }
}