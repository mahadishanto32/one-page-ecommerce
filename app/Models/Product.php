<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'quantity',
        'stock_status',
        'previous_price',
        'sku',
        'min_order',
        'max_order',
        'featured_image',
        'gallery_images',
        'video',
        'category_id',
        'product_unit',
        'specification',
        'product_tags',
        'is_featured',
        'is_show_frontend',
        'size_chart',
        'status',
        'product_attribute_id',
        'product_attribute_option_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'previous_price' => 'decimal:2',
        'quantity' => 'integer',
        'min_order' => 'integer',
        'max_order' => 'integer',
        'gallery_images' => 'array',
        'specification' => 'array',
        'product_tags' => 'array',
        'size_chart' => 'array',
        'is_featured' => 'boolean',
        'has_featured' => 'boolean',
        'is_show_frontend' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $baseSlug = Str::slug($product->name);
                $slug = $baseSlug;
                $count = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $product->slug = $slug;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function productAttributeOption(): BelongsTo
    {
        return $this->belongsTo(ProductAttributeOption::class, 'product_attribute_option_id');
    }

    public function productAttributeVariants(): HasMany
    {
        return $this->hasMany(ProductAttributeVariant::class);
    }

    // ========== HELPER METHODS (Renamed to avoid conflicts) ==========

    public function hasProductAttribute(): bool
    {
        return !is_null($this->product_attribute_id);
    }

    /**
     * Check if product has an assigned attribute option
     */
    public function hasProductAttributeOption(): bool
    {
        return !is_null($this->product_attribute_option_id);
    }

    /**
     * Check if product has both attribute and option assigned
     */
    public function hasFullAttribute(): bool
    {
        return $this->hasProductAttribute() && $this->hasProductAttributeOption();
    }

    /**
     * Get the attribute name (if exists)
     */
    public function getProductAttributeNameAttribute(): ?string
    {
        return $this->productAttribute?->name;
    }

    /**
     * Get the attribute option name (if exists)
     */
    public function getProductAttributeOptionNameAttribute(): ?string
    {
        return $this->productAttributeOption?->name;
    }

    /**
     * Get the full attribute with option description
     */
    public function getProductAttributeDescriptionAttribute(): ?string
    {
        if ($this->productAttribute && $this->productAttributeOption) {
            return $this->productAttribute->name . ': ' . $this->productAttributeOption->name;
        }
        return null;
    }

    /**
     * Get price with attribute option added price
     */
    public function getFinalPriceAttribute(): float
    {
        $basePrice = (float) $this->price;
        
        if ($this->productAttributeOption && $this->productAttributeOption->added_price) {
            return $basePrice + (float) $this->productAttributeOption->added_price;
        }
        
        return $basePrice;
    }

    /**
     * Get available attribute options for this product's attribute
     */
    public function getAvailableAttributeOptionsAttribute()
    {
        if ($this->productAttribute) {
            return $this->productAttribute->activeOptions;
        }
        return collect();
    }

    /**
     * Scope for products with specific attribute
     */
    public function scopeWithProductAttribute($query, $attributeId)
    {
        return $query->where('product_attribute_id', $attributeId);
    }

    /**
     * Scope for products with specific attribute option
     */
    public function scopeWithProductAttributeOption($query, $optionId)
    {
        return $query->where('product_attribute_option_id', $optionId);
    }

    /**
     * Scope for products with active attribute
     */
    public function scopeWithActiveProductAttribute($query)
    {
        return $query->whereHas('productAttribute', function ($q) {
            $q->where('status', true);
        });
    }

    /**
     * Scope for products with active attribute option
     */
    public function scopeWithActiveProductAttributeOption($query)
    {
        return $query->whereHas('productAttributeOption', function ($q) {
            $q->where('status', true);
        });
    }

    /**
     * Scope for products that have any attribute assigned
     */
    public function scopeHasAttributeAssigned($query)
    {
        return $query->whereNotNull('product_attribute_id');
    }

    /**
     * Scope for products that have any attribute option assigned
     */
    public function scopeHasAttributeOptionAssigned($query)
    {
        return $query->whereNotNull('product_attribute_option_id');
    }

    public function scopeHasVariantStock($query)
    {
        return $query->whereHas('productAttributeVariants', function ($q) {
            $q->where('quantity', '>', 0);
        });
    }

    public function getVariantTotalQuantityAttribute(): int
    {
        if ($this->productAttributeVariants->isEmpty()) {
            return (int) $this->quantity;
        }

        return (int) $this->productAttributeVariants->sum('quantity');
    }

    public function productAttributeVariantCombinations()
    {
        return $this->hasMany(ProductAttributeVariantCombination::class);
    }

    public function attributeOptionImages()
    {
        return $this->hasMany(ProductAttributeOptionImage::class);
    }
}