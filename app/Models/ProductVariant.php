<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'options',
        'price',
        'compare_price',
        'stock_quantity',
        'stock_status',
        'is_active',
        'sort_order',
    ];

    // casts
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'compare_price' => 'decimal:2',
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // local scopes
    #[Scope]
    public function active(Builder $query)
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    public function inStock(Builder $query)
    {
        return $query->where('stock_quantity', '>', 0)->where('stock_status', 'in_stock');
    }

    #[Scope]
    public function outOfStock(Builder $query)
    {
        return $query->where('stock_quantity', '=', 0)->where('stock_status', 'out_of_stock');
    }

    // relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // helper methods
    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round(($this->compare_price - $this->price) / $this->compare_price * 100);
        }
        return 0;
    }

    // events
    protected static function boot()
    {
        parent::boot();

        static::creating(function (ProductVariant $variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });
    }
}
