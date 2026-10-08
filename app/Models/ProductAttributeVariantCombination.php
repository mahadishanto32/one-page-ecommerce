<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeVariantCombination extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_attribute_one_id',
        'product_attribute_two_id',
        'product_option_attribute_one_id',
        'product_attribute_option_two_id',
        'image',
        'sku',
        'quantity',
        'additional_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'additional_price' => 'decimal:2',
    ];

    /**
     * The product this combination belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The attribute this combination belongs to.
     */
    public function productAttribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    /**
     * First attribute option (e.g. Color: Red).
     */
    public function optionOne()
    {
        return $this->belongsTo(ProductAttributeOption::class, 'product_option_attribute_one_id');
    }

    /**
     * Second attribute option (e.g. Size: Large).
     */
    public function optionTwo()
    {
        return $this->belongsTo(ProductAttributeOption::class, 'product_attribute_option_two_id');
    }
}