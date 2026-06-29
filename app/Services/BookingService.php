<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
}
