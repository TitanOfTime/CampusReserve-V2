<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingValidationTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $bookingService;
    private User $user;
    private Room $standardRoom;
    private Room $premiumRoom;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = new BookingService();
        $this->user = User::factory()->create(['is_premium' => false]);
        $this->standardRoom = Room::create([
            'name' => 'Standard Study Room',
            'location' => 'Block A',
            'capacity' => 4,
            'is_premium' => false,
        ]);
        $this->premiumRoom = Room::create([
            'name' => 'Premium Boardroom',
            'location' => 'Block C',
            'capacity' => 12,
            'is_premium' => true,
        ]);
    }

    public function test_it_allows_valid_booking_within_operating_hours()
    {
        $startTime = Carbon::now()->addDay()->setTime(9, 0, 0); // 9:00 AM tomorrow
        $endTime = $startTime->copy()->addHours(2); // 11:00 AM

        $booking = $this->bookingService->createBooking($this->user, [
            'room_id' => $this->standardRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'room_id' => $this->standardRoom->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_it_fails_when_booking_in_the_past()
    {
        $startTime = Carbon::now()->subHours(2);
        $endTime = $startTime->copy()->addHour();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The booking start time must be in the future.');

        $this->bookingService->createBooking($this->user, [
            'room_id' => $this->standardRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);
    }

    public function test_it_fails_when_booking_duration_exceeds_4_hours()
    {
        $startTime = Carbon::now()->addDay()->setTime(9, 0, 0);
        $endTime = $startTime->copy()->addMinutes(241); // 4 hours 1 minute

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Bookings cannot exceed a maximum duration of 4 hours.');

        $this->bookingService->createBooking($this->user, [
            'room_id' => $this->standardRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);
    }

    public function test_it_fails_when_booking_starts_before_7_am()
    {
        $startTime = Carbon::now()->addDay()->setTime(6, 59, 0); // 6:59 AM
        $endTime = $startTime->copy()->addHours(2);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Bookings cannot start before the university opening time of 7:00 AM.');

        $this->bookingService->createBooking($this->user, [
            'room_id' => $this->standardRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);
    }

    public function test_it_fails_when_booking_ends_after_8_pm()
    {
        $startTime = Carbon::now()->addDay()->setTime(18, 0, 0); // 6:00 PM
        $endTime = $startTime->copy()->setTime(20, 1, 0); // 8:01 PM

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Bookings must end before the university closing time of 8:00 PM.');

        $this->bookingService->createBooking($this->user, [
            'room_id' => $this->standardRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);
    }

    public function test_it_fails_when_booking_crosses_calendar_days()
    {
        $startTime = Carbon::now()->addDay()->setTime(19, 0, 0); // 7:00 PM
        $endTime = $startTime->copy()->addDay()->setTime(8, 0, 0); // 8:00 AM next day

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Bookings must start and end on the same calendar day.');

        $this->bookingService->createBooking($this->user, [
            'room_id' => $this->standardRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);
    }

    public function test_it_fails_when_non_premium_user_books_premium_room()
    {
        $startTime = Carbon::now()->addDay()->setTime(9, 0, 0);
        $endTime = $startTime->copy()->addHours(2);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Only premium users can book premium rooms.');

        $this->bookingService->createBooking($this->user, [
            'room_id' => $this->premiumRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);
    }

    public function test_it_allows_premium_user_to_book_premium_room()
    {
        $premiumUser = User::factory()->create(['is_premium' => true]);
        $startTime = Carbon::now()->addDay()->setTime(9, 0, 0);
        $endTime = $startTime->copy()->addHours(2);

        $booking = $this->bookingService->createBooking($premiumUser, [
            'room_id' => $this->premiumRoom->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'room_id' => $this->premiumRoom->id,
        ]);
    }
}
