<?php

namespace App\Modifiers;

use Closure;
use GetCandy\Base\OrderModifier as GetCandyOrderModifier;
use GetCandy\Models\Cart;
use GetCandy\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderModifier extends GetCandyOrderModifier
{
    public function creating(Cart $cart, Closure $next): Cart
    {
        //...
        return $next($cart);
    }

    public function created(Order $order, Closure $next): Order
    {
        if (Auth::user()->customers->count()) {
            if ($customerId = Auth::user()->customers->first()?->id) {
                $order->fill([
                    'customer_id' => $customerId
                ])->save();
            }
        }
        return $next($order);
    }
}