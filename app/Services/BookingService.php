<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Attempt to create a booking with validation.
     *
     * @throws ValidationException
     */
    public function createBooking(User $user, array $data): Booking
    {
        $this->applyRateLimit($user);
        $this->validateBookingTimes($data['start_time'], $data['end_time']);

        return DB::transaction(function () use ($user, $data) {
            $room = Room::query()
                ->select(['id', 'is_premium'])
                ->findOrFail($data['room_id']);

            if ($room->is_premium && !$user->is_premium) {
                throw ValidationException::withMessages([
                    'room_id' => 'Only premium users can book premium rooms.',
                ]);
            }

            $overlap = Booking::query()
                ->forRoom($data['room_id'])
                ->confirmed()
                ->overlapping($data['start_time'], $data['end_time'])
                ->lockForUpdate()
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'start_time' => 'This room is already booked for the selected time slot.',
                ]);
            }

            return $user->bookings()->create([
                'room_id' => $data['room_id'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'purpose' => $data['purpose'] ?? null,
                'status' => 'confirmed',
            ]);
        });
    }

    /**
     * Attempt to update an existing booking with validation.
     *
     * @throws ValidationException
     */
    public function updateBooking(Booking $booking, array $data): Booking
    {
        $user = $booking->user;
        $this->applyRateLimit($user);
        $this->validateBookingTimes($data['start_time'], $data['end_time']);

        return DB::transaction(function () use ($booking, $data) {
            $room = Room::query()
                ->select(['id', 'is_premium'])
                ->findOrFail($booking->room_id);

            $booking->loadMissing('user');
            $user = $booking->user;

            if ($room->is_premium && !$user->is_premium) {
                throw ValidationException::withMessages([
                    'room_id' => 'Only premium users can book premium rooms.',
                ]);
            }

            $overlap = Booking::query()
                ->forRoom($booking->room_id)
                ->confirmed()
                ->whereKeyNot($booking->id)
                ->overlapping($data['start_time'], $data['end_time'])
                ->lockForUpdate()
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'newStartTime' => 'This room is already booked for the selected time slot.',
                ]);
            }

            $booking->update([
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);

            return $booking->fresh();
        });
    }

    /**
     * Apply programmatic rate limiting for booking actions.
     *
     * @throws ValidationException
     */
    private function applyRateLimit(User $user): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $rateLimitKey = 'booking-action:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            throw ValidationException::withMessages([
                'start_time' => "Too many booking attempts. Please try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($rateLimitKey, 60);
    }

    /**
     * Perform all logical checks on start and end times.
     *
     * @throws ValidationException
     */
    private function validateBookingTimes($startTime, $endTime): void
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        // 1. End time must be after start time
        if ($end->lte($start)) {
            throw ValidationException::withMessages([
                'end_time' => 'The end time must be after the start time.',
            ]);
        }

        // 2. Start time must be in the future (give a 1-minute grace buffer for request delays)
        if ($start->lt(Carbon::now()->subMinute())) {
            throw ValidationException::withMessages([
                'start_time' => 'The booking start time must be in the future.',
            ]);
        }

        // 3. Must be on the exact same calendar day
        if (!$start->isSameDay($end)) {
            throw ValidationException::withMessages([
                'end_time' => 'Bookings must start and end on the same calendar day.',
            ]);
        }

        // 4. Operating hours constraint: between 7:00 AM and 8:00 PM (07:00 to 20:00)
        $operatingStart = $start->copy()->setTime(7, 0, 0);
        $operatingEnd = $start->copy()->setTime(20, 0, 0);

        if ($start->lt($operatingStart)) {
            throw ValidationException::withMessages([
                'start_time' => 'Bookings cannot start before the university opening time of 7:00 AM.',
            ]);
        }

        if ($end->gt($operatingEnd)) {
            throw ValidationException::withMessages([
                'end_time' => 'Bookings must end before the university closing time of 8:00 PM.',
            ]);
        }

        // 5. Maximum duration of 4 hours (240 minutes)
        if ($start->diffInMinutes($end) > 240) {
            throw ValidationException::withMessages([
                'end_time' => 'Bookings cannot exceed a maximum duration of 4 hours.',
            ]);
        }
    }
}
