<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class MyBookings extends Component
{
    public $bookings;

    // Edit modal state
    public $showEditModal   = false;
    public $editingBooking  = null;
    public $newStartTime;
    public $newEndTime;

    public function mount(): void
    {
        $this->loadBookings();
    }

    private function loadBookings(): void
    {
        $this->bookings = auth()->user()
            ->bookings()
            ->with('room')
            ->where('status', 'confirmed')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();
    }

    // ─── Cancel ──────────────────────────────────────────────────────────────

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);

        // Security: ensure this booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Time check: cannot cancel a booking that has already started
        if ($booking->start_time->isPast()) {
            session()->flash('error', 'Cannot cancel a booking that has already started.');
            return;
        }

        $booking->update(['status' => 'cancelled']);
        $this->loadBookings();
        session()->flash('success', 'Booking cancelled successfully.');
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function editBooking(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $this->editingBooking = $booking;
        $this->newStartTime   = $booking->start_time->format('Y-m-d\TH:i');
        $this->newEndTime     = $booking->end_time->format('Y-m-d\TH:i');
        $this->resetErrorBag();
        $this->showEditModal  = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal  = false;
        $this->editingBooking = null;
        $this->newStartTime   = null;
        $this->newEndTime     = null;
        $this->resetErrorBag();
    }

    public function saveBooking(BookingService $bookingService): void
    {
        $this->validate([
            'newStartTime' => 'required|date|after_or_equal:now',
            'newEndTime'   => 'required|date|after:newStartTime',
        ]);

        try {
            $bookingService->updateBooking($this->editingBooking, [
                'start_time' => $this->newStartTime,
                'end_time'   => $this->newEndTime,
            ]);

            $this->closeEditModal();
            $this->loadBookings();
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
