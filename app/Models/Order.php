<?php

namespace App\Models;

use App\Models\User;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\OrderPayment;
use App\Models\OrderShipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'customer_id',
        'coupon_id',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'tax_amount',
        'total',
        'order_status',
        'customer_ip',
    ];

    // Scopes
    #[Scope]
    protected function ofStatus(Builder $query, string $status): void
    {
        $query->where('order_status', $status);
    }


    #[Scope]
    protected function pending(Builder $query)
    {
        $query->where('order_status', 'pending');
    }

    #[Scope]
    protected function processing(Builder $query)
    {
        $query->where('order_status', 'processing');
    }

    #[Scope]
    protected function shipped(Builder $query)
    {
        $query->where('order_status', 'shipped');
    }

    #[Scope]
    protected function delivered(Builder $query)
    {
        $query->where('order_status', 'delivered');
    }

    #[Scope]
    protected function cancelled(Builder $query)
    {
        $query->where('order_status', 'cancelled');
    }

    #[Scope]
    protected function returned(Builder $query)
    {
        $query->where('order_status', 'returned');
    }

    #[Scope]
    protected function ofPaymentStatus(Builder $query, string $status): void
    {
        $query->whereHas('payment', function ($q) use ($status) {
            $q->where('payment_status', $status);
        });
    }

    // helper methods

    public function getFullShippingAddressAttribute()
    {
        $address = $this->shippingAddress;
        //If you use $this->shippingAddress(), it returns the Relationship Builder (so you can add more filters like ->where(...)).
        //If you use $this->shippingAddress (no parentheses), it returns the Result (the actual address object).
        if (!$address) {
            return 'No address linked';
        }
        // array_filter removes null, false, or empty string values
        return implode(', ', array_filter([
            $address->address_line_1,
            $address->address_line_2,
            $address->city,
            $address->state,
            $address->postal_code,
            $address->country,
        ]));
    }

    public function updateStatus($newStatus, $notes = null, $userId = null)
    {
        // 1. Update the current order status
        $this->update(['order_status' => $newStatus]);

        // 2. Create a history record (using the relationship)
        $this->statusHistories()->create([
            // order_id is set automatically by the relationship
            'status'  => $newStatus,
            'notes'   => $notes,
            'user_id' => $userId,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD' . strtoupper(uniqid());
            }
        });

        static::created(function ($order) {
            // Create history record
            $order->statusHistories()->create([
                'status' => $order->order_status,
                'notes' => 'Order created',
            ]);
        });

        // send an order confirmation email to the customer
    }

    // Relationships

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }

    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'billing');
    }

    public function payment()
    {
        return $this->hasOne(OrderPayment::class);
    }

    public function shipment()
    {
        return $this->hasOne(OrderShipment::class);
    }




    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
