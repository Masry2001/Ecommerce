<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Support\Str;

class Coupon extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'usage_limit_per_customer',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    //Local Scope: Automatically receives the $query argument from Laravel
    //Scopes keep the SQL logic inside the Model, which follows the "Fat Model, Skinny Controller" best practice.
    #[Scope]
    protected function active(Builder $query): Builder
    {
        $now = now();
        return $query->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', $now);
            });
    }

    #[Scope]
    protected function valid(Builder $query): Builder
    {
        return $query->active();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($coupon) {
            if (empty($coupon->code)) {
                $coupon->code = strtoupper(Str::random(8));
            }
        });
    }

    // relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }


    // methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function isStarted()
    {
        return $this->starts_at && $this->starts_at <= now();
    }

    // check the usage limit
    public function isUsageLimitReached()
    {
        return $this->usage_limit && $this->usages()->count() >= $this->usage_limit;
    }

    public function isValid()
    {
        return $this->is_active && !$this->isExpired() && $this->isStarted() && !$this->isUsageLimitReached();
    }

    public function canBeUsedByCustomer(Customer $customer)
    {
        if (!$this->isValid()) {
            return false;
        }
        return $this->usage_limit_per_customer && ($this->usages()->where('customer_id', $customer->id)->count() < $this->usage_limit_per_customer);
    }

    public function calculateDiscount($orderValue)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->min_order_value && $orderValue < $this->min_order_value) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = $orderValue * ($this->value / 100);
            return $this->max_discount
                ? min($discount, $this->max_discount)
                : $discount;
        }

        return $this->value;
    }
}
