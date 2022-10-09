<?php

namespace App\Http\Livewire;

use GetCandy\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Orders extends Component
{
    public function render()
    {
        return view('livewire.orders');
    }

    public function getOrdersProperty() {
        return Auth::user()->customers->first()->orders()->paginate();
    }
}
