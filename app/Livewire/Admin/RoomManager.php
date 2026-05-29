<?php

namespace App\Livewire\Admin;

use App\Models\Room;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RoomManager extends Component
{
    // Form fields state
    public $name;
    public $location;
    public $capacity;
    public $description;
    public $is_premium = false;
    public $image_url;
    public $editingRoomId = null;

    #[Computed]
    public function rooms()
    {
        return Room::orderBy('name')->get();
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1',
            'description' => 'required|string',
            'is_premium'  => 'boolean',
            'image_url'   => 'required|url',
        ];
    }

    public function editRoom(int $id): void
    {
        $room = Room::findOrFail($id);
        
        $this->editingRoomId = $room->id;
        $this->name          = $room->name;
        $this->location      = $room->location;
        $this->capacity      = $room->capacity;
        $this->description  = $room->description;
        $this->is_premium    = (bool)$room->is_premium;
        $this->image_url     = $room->image_url;

        $this->resetErrorBag();
    }

    public function resetForm(): void
    {
        $this->editingRoomId = null;
        $this->name          = null;
        $this->location      = null;
        $this->capacity      = null;
        $this->description  = null;
        $this->is_premium    = false;
        $this->image_url     = null;

        $this->resetErrorBag();
    }

    public function saveRoom(): void
    {
        $data = $this->validate();

        if ($this->editingRoomId) {
            $room = Room::findOrFail($this->editingRoomId);
            $room->update($data);
            session()->flash('success', 'Room updated successfully.');
        } else {
            Room::create($data);
            session()->flash('success', 'Room created successfully.');
        }

        $this->resetForm();
    }

    public function deleteRoom(int $id): void
    {
        $room = Room::findOrFail($id);
        $room->delete();
        
        if ($this->editingRoomId === $id) {
            $this->resetForm();
        }

        session()->flash('success', 'Room deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.room-manager');
    }
}
