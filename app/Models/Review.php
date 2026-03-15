<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasUuids;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'reviews';

    protected $fillable = [
        'product_id',
        'customer_id',
        'rating',
        'comment',
    ];
}
