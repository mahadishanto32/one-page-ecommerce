<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Boot the model to auto-generate slug
     */
    protected static function booted(): void
    {
        static::creating(function (ProductAttribute $productAttribute) {
            if (empty($productAttribute->slug)) {
                $baseSlug = Str::slug($productAttribute->name);
                $slug = $baseSlug;
                $count = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $productAttribute->slug = $slug;
            }
        });
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductAttributeOption::class, 'attribute_id');
    }

    public function activeOptions(): HasMany
    {
        return $this->hasMany(ProductAttributeOption::class, 'attribute_id')
            ->where('status', true);
    }
}