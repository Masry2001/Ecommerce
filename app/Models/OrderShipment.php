<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderShipment extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'order_shipments';

    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
