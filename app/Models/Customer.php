<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Review;
use App\Models\Address;
use App\Models\CouponUsage;

class Customer extends Model
{
    use SoftDeletes;
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'date_of_birth',
        'gender',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function active(Builder $query)
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function inactive(Builder $query)
    {
        $query->where('is_active', false);
    }

    // relations
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function getTotalSpentAttribute()
    {
        return $this->orders()->where('payment_status', 'paid')->sum('total');
    }

    public function getOrdersCountAttribute()
    {
        return $this->orders()->count();
    }

    public function getAverageOrderValueAttribute()
    {
        return $this->orders()->where('payment_status', 'paid')->avg('total');
    }

    public function getFirstOrderDateAttribute()
    {
        return $this->orders()->min('created_at');
    }

    public function getLastOrderDateAttribute()
    {
        return $this->orders()->max('created_at');
    }
}
