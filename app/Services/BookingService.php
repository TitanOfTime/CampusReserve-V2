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
            // 1. Premium room access check
            $room = Room::findOrFail($data['room_id']);
            if ($room->is_premium && !$user->is_premium) {
                throw ValidationException::withMessages([
                    'room_id' => 'Only premium users can book premium rooms.',
                ]);
            }

            // 2. Double-booking check (DB-level with row locking)
            $overlap = Booking::where('room_id', $data['room_id'])
                ->where('status', 'confirmed')
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->lockForUpdate()
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'start_time' => 'This room is already booked for the selected time slot.',
                ]);
            }

            // 3. Create the booking
            return $user->bookings()->create([
                'room_id' => $data['room_id'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'purpose' => $data['purpose'] ?? null,
                'status' => 'confirmed',
            ]);
        });
    }
}
