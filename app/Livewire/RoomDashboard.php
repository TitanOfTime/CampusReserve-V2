<?php

namespace App\Livewire;

use App\Models\Room;
use App\Services\BookingService;
use App\Services\RoomRecommendationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RoomDashboard extends Component
{
    public $selectedRoom = null;
    public $startTime;
    public $endTime;
    public $showModal = false;
    public string $preference = '';
    public array $recommendation = [];

    #[Computed]
    public function rooms()
    {
        return Cache::remember('rooms.catalog.all', now()->addMinutes(10), function () {
            return Room::query()
                ->catalog()
                ->orderedForCatalog()
                ->get();
        });
    }

    public function openModal($roomId)
    {
        $this->selectedRoom = $this->rooms->firstWhere('id', (int) $roomId)
            ?? Room::query()->catalog()->findOrFail($roomId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->selectedRoom = null;
        $this->startTime = null;
        $this->endTime = null;
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function suggestRooms(RoomRecommendationService $recommendationService): void
    {
        $this->validate([
            'preference' => 'nullable|string|max:500',
        ]);

        $key = 'suggest-rooms:'.auth()->id();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'preference' => "Too many matching attempts. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        $this->recommendation = $recommendationService->recommend(
            auth()->user(),
            $this->preference,
            $this->rooms,
        );
    }

    public function clearRecommendation(): void
    {
        $this->preference = '';
        $this->recommendation = [];
    }

    public function bookRoom(BookingService $bookingService)
    {
        $this->validate([
            'startTime' => 'required|date|after_or_equal:now',
            'endTime'   => 'required|date|after:startTime',
        ]);

        try {
            $bookingService->createBooking(auth()->user(), [
                'room_id' => $this->selectedRoom->id,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
            ]);

            $this->closeModal();
            session()->flash('success', 'Room booked successfully!');
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $key => $messages) {
                // Map the service validation keys to our Livewire component properties
                if ($key === 'start_time') {
                    $errors['startTime'] = $messages;
                } elseif ($key === 'end_time') {
                    $errors['endTime'] = $messages;
                } else {
                    $errors[$key] = $messages;
                }
            }
            throw ValidationException::withMessages($errors);
        }
    }

    public function render()
    {
        return view('livewire.room-dashboard');
    }
}
