<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasUuids;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'manage_stock',
        'stock_status',
        'is_active',
        'is_featured',
        'has_variants',
        'weight',
        'meta_title',
        'meta_description',
        'views_count',
    ];

    //casts
    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'manage_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'has_variants' => 'boolean',
        'weight' => 'decimal:2',
        'views_count' => 'integer',
    ];

    // local scopes

    #[Scope]
    protected function inCategory(Builder $query, string $category_id)
    {
        return $query->where('category_id', $category_id);
    }

    #[Scope]
    protected function ofBrand(Builder $query, string $brand_id)
    {
        return $query->where('brand_id', $brand_id);
    }

    #[Scope]
    protected function active(Builder $query)
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function featured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    #[Scope]
    protected function inStock(Builder $query)
    {
        return $query->where('stock_status', 'in_stock')->where('stock_quantity', '>', 0);
    }

    #[Scope]
    protected function outOfStock(Builder $query)
    {
        return $query->where('stock_status', 'out_of_stock')->where('stock_quantity', 0);
    }

    #[Scope]
    protected function lowStock(Builder $query)
    {
        return $query->where('stock_status', 'low_stock')->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0);
    }

    #[Scope]
    protected function onBackorder(Builder $query)
    {
        return $query->where('stock_status', 'on_backorder');
    }

    #[Scope]
    protected function inPriceRange(Builder $query, float $min, float $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    #[Scope]
    protected function hasVariants(Builder $query)
    {
        return $query->where('has_variants', true);
    }

    #[Scope]
    protected function hasNoVariants(Builder $query)
    {
        return $query->where('has_variants', false);
    }

    #[Scope]
    protected function withLowStock(Builder $query)
    {
        return $query->whereColumn('stock_quantity', '<', 'low_stock_threshold');
    }

    #[Scope]
    protected function withoutLowStock(Builder $query)
    {
        return $query->whereColumn('stock_quantity', '>=', 'low_stock_threshold');
    }

    #[Scope]
    protected function withManageStock(Builder $query)
    {
        return $query->where('manage_stock', true);
    }

    #[Scope]
    protected function withoutManageStock(Builder $query)
    {
        return $query->where('manage_stock', false);
    }

    #[Scope]
    protected function withStockStatus(Builder $query, string $status)
    {
        return $query->where('stock_status', $status);
    }

    #[Scope]
    protected function withoutStockStatus(Builder $query, string $status)
    {
        return $query->where('stock_status', '!=', $status);
    }


    // Relations

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id')->orderBy('sort_order', 'asc');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')->orderBy('sort_order', 'asc');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id')->where('is_approved', true);
    }

    public function pendingReviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id')->where('is_approved', false);
    }

    // Helper methods
    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round(($this->compare_price - $this->price) / $this->compare_price * 100);
        }
        return 0;
    }

    public function getAverageRatingAttribute()
    {
        return round($this->approvedReviews()->avg('rating'), 1);
    }

    public function getReviewsCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function incrementViewsCount()
    {
        $this->increment('views_count');
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function (Product $product) {
            if (empty($product->slug) && $product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    protected static function booted()
    {
        static::saving(function ($product) {
            if ($product->stock_quantity == 0) {
                $product->stock_status = 'out_of_stock';
            } elseif ($product->stock_quantity <= $product->low_stock_threshold) {
                $product->stock_status = 'low_stock';
            } else {
                $product->stock_status = 'in_stock';
            }
        });
    }
}
