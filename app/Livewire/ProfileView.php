<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileView extends Component
{
    public $user;

    public function mount(): void
    {
        $this->user = auth()->user();
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect('/');
    }

    public function render()
    {
        return view('livewire.profile-view');
    }
}
