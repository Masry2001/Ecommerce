<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Models\Product;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasUuids, HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    #[Scope]
    protected function active(Builder $query)
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function sorted(Builder $query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
