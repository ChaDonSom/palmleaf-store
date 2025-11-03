<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\Order;

class CheckoutSuccessPage extends Component
{
    public ?Cart $cart;

    public Order $order;

    public function mount(): void
    {
        // Prefer the order ID stored in session after checkout completes.
        $orderId = session()->pull('last_order_id');

        if ($orderId && $order = Order::find($orderId)) {
            $this->order = $order;
        } else {
            // Fallback: attempt to resolve from the current cart if available
            $this->cart = CartSession::current();
            if ($this->cart && $this->cart->completedOrder) {
                $this->order = $this->cart->completedOrder;
            } else {
                $this->redirect('/');

                return;
            }
        }

        // Clear any cart left in session now that checkout is successful.
        CartSession::forget();
    }

    public function render(): View
    {
        return view('livewire.checkout-success-page');
    }
}
