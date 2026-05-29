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

    /**
     * Attempt to update an existing booking with validation.
     *
     * @throws ValidationException
     */
    function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            // 1. Premium room access check (room may have changed if ever extended)
            $room = Room::findOrFail($booking->room_id);
            $user = $booking->user;
            if ($room->is_premium && !$user->is_premium) {
                throw ValidationException::withMessages([
                    'room_id' => 'Only premium users can book premium rooms.',
                ]);
            }

            // 2. Double-booking check — EXCLUDES the booking being edited
            $overlap = Booking::where('room_id', $booking->room_id)
                ->where('status', 'confirmed')
                ->where('id', '!=', $booking->id)   // <-- key difference
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->lockForUpdate()
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'newStartTime' => 'This room is already booked for the selected time slot.',
                ]);
            }

            // 3. Apply the update
            $booking->update([
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
            ]);

            return $booking->fresh();
        });
    }
}
