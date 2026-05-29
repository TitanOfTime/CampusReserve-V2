<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;

class RoomDashboard extends Component
{
    public $rooms;
    public $selectedRoom = null;
    public $startTime;
    public $endTime;
    public $showModal = false;

    public function mount()
    {
        $this->rooms = Room::all();
    }

    public function openModal($roomId)
    {
        $this->selectedRoom = Room::find($roomId);
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

    public function bookRoom(BookingService $bookingService)
    {
        $this->validate([
            'startTime' => 'required|date|after:now',
            'endTime' => 'required|date|after:startTime',
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
                if ($key === 'start_time') $errors['startTime'] = $messages;
                elseif ($key === 'end_time') $errors['endTime'] = $messages;
                else $errors[$key] = $messages;
            }
            throw ValidationException::withMessages($errors);
        }
    }

    public function render()
    {
        return view('livewire.room-dashboard');
    }
}
