<?php

namespace App\Modifiers;

use Closure;
use Lunar\Base\OrderModifier as LunarOrderModifier;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderModifier extends LunarOrderModifier
{
    public function creating(Cart $cart, Closure $next): Cart
    {
        //...
        return $next($cart);
    }

    public function created(Order $order, Closure $next): Order
    {
        if (Auth::user()?->customers?->count()) {
            if ($customerId = Auth::user()->customers->first()?->id) {
                $order->fill([
                    'customer_id' => $customerId
                ])->save();
            }
        }
        return $next($order);
    }
}