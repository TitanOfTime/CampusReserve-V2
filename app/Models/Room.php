<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'location', 'capacity', 'is_premium', 'description'];

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