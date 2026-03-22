<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;


class Review extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'reviews';

    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'cusomter_name',
        'cusomter_email',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'is_approved',
    ];

    // casts
    protected function casts()
    {
        return [
            'rating' => 'integer',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    // scopes
    #[Scope]
    public function approved(Builder $query)
    {
        return $query->where('is_approved', true);
    }

    #[Scope]
    public function verified(Builder $query)
    {
        return $query->where('is_verified_purchase', true);
    }

    #[Scope]
    public function rating(Builder $query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    // relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
