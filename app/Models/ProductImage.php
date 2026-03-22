<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class ProductImage extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image_path',
        'alt_text',
        'is_primary',
        'sort_order'
    ];

    // casts
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // local scopes
    #[Scope]
    public function primary(Builder $query)
    {
        return $query->where('is_primary', true);
    }

    // relations
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }

    // helper methods
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getAltTextAttribute()
    {
        return $this->alt_text ?? null;
    }
}
