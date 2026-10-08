<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeOptionImage extends Model
{
    protected $fillable = [
        'product_id',
        'product_attribute_option_id',
        'image_path',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function attributeOption()
    {
        return $this->belongsTo(
            ProductAttributeOption::class,
            'product_attribute_option_id'
        );
    }
}