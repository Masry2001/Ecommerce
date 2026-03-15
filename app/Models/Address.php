<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Address extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'addresses';

    protected $fillable = [
        'customer_id',
        'full_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
        'type'
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }


    #[Scope]
    protected function default(Builder $query)
    {
        $query->where('is_default', true);
    }

    #[Scope]
    protected function ofType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function getFullAddressAttribute()
    {
        return implode(', ', array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]));
    }


    // relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
