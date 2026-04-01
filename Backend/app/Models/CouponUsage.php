<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CouponUsage extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'coupon_usages';

    protected $fillable = [
        'coupon_id',
        'customer_id',
        'order_id',
        'coupon_code',
        'customer_email',
        'discount_amount',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
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
