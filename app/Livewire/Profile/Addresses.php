<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Addresses extends Component
{
    public function render()
    {
        return view('livewire.profile.addresses');
    }

    public $addresses = [];

    public function mount()
    {
        $this->addresses = Auth::user()->customers->first()->addresses;
    }
}
