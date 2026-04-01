<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderPayment extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'order_payments';

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_status',
        'transaction_id',
        'amount',
        'currency',
        'gateway_response',
    ];

    protected $casts = [
        'gateway_response' => 'array', // this will handle encoding and decoding json automatically
        'amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
