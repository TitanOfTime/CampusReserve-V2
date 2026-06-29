<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MyBookings extends Component
{
    public $showEditModal = false;
    public $editingBooking = null;
    public $newStartTime;
    public $newEndTime;

    #[Computed]
    public function bookings()
    {
        return auth()->user()
            ->bookings()
            ->select(['id', 'user_id', 'room_id', 'start_time', 'end_time', 'status'])
            ->with('room')
            ->confirmed()
            ->upcoming()
            ->orderBy('start_time')
            ->get();
    }

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::query()
            ->forUser(auth()->id())
            ->findOrFail($bookingId);

        if ($booking->start_time->isPast()) {
            session()->flash('error', 'Cannot cancel a booking that has already started.');
            return;
        }

        $booking->update(['status' => 'cancelled']);
        session()->flash('success', 'Booking cancelled successfully.');
    }

    public function editBooking(int $bookingId): void
    {
        $booking = Booking::query()
            ->with('room')
            ->forUser(auth()->id())
            ->findOrFail($bookingId);

        $this->editingBooking = $booking;
        $this->newStartTime = $booking->start_time->format('Y-m-d\TH:i');
        $this->newEndTime = $booking->end_time->format('Y-m-d\TH:i');
        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingBooking = null;
        $this->newStartTime = null;
        $this->newEndTime = null;
        $this->resetErrorBag();
    }

    public function saveBooking(BookingService $bookingService): void
    {
        $this->validate([
            'newStartTime' => 'required|date|after_or_equal:now',
            'newEndTime' => 'required|date|after:newStartTime',
        ]);

        try {
            $bookingService->updateBooking($this->editingBooking, [
                'start_time' => $this->newStartTime,
                'end_time' => $this->newEndTime,
            ]);

            $this->closeEditModal();
            session()->flash('success', 'Booking updated successfully.');
        } catch (ValidationException $e) {
            throw ValidationException::withMessages($e->errors());
        }
    }

    public function render()
    {
        return view('livewire.my-bookings');
    }
}
