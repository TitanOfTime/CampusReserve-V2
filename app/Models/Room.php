<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Room extends Model
{
    public const CATALOG_CACHE_KEYS = [
        'rooms.catalog.all',
        'rooms.catalog.premium',
        'rooms.catalog.standard',
    ];

    protected $fillable = ['name', 'location', 'capacity', 'is_premium', 'description', 'image_url'];

    protected static function booted(): void
    {
        static::saved(fn () => self::flushCatalogCache());
        static::deleted(fn () => self::flushCatalogCache());
    }

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public static function flushCatalogCache(): void
    {
        foreach (self::CATALOG_CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    public function scopeCatalog($query)
    {
        return $query->select([
            'id',
            'name',
            'location',
            'capacity',
            'is_premium',
            'description',
            'image_url',
            'updated_at',
        ]);
    }

    public function scopeOrderedForCatalog($query)
    {
        return $query
            ->orderByDesc('is_premium')
            ->orderBy('capacity')
            ->orderBy('name');
    }

    public function scopeStandard($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeAvailable($query, $startTime, $endTime)
    {
        return $query->whereDoesntHave('bookings', function ($q) use ($startTime, $endTime) {
            $q->where('status', 'confirmed')
              ->where('start_time', '<', $endTime)
              ->where('end_time', '>', $startTime);
        });
    }
}
