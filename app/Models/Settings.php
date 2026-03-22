<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Settings extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group'
    ];



    // scopes
    #[Scope]
    public function group(Builder $query, string $group)
    {
        return $query->where('group', $group);
    }

    // helper methods
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if ($setting) {
            return static::castValue($setting->value, $setting->type);
        }
        return $default;
    }

    public static function set(string $key, $value, string $type = 'string', string $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key], // Match the record by the key
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );
    }

    protected static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            'decimal', 'float' => (float) $value,
            default => $value,
        };
    }
}
