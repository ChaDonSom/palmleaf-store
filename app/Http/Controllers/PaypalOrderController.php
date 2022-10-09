<?php

namespace App\Http\Controllers;

use App\Managers\PaypalManager;
use GetCandy\Facades\Payments;
use GetCandy\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaypalOrderController extends Controller
{
    public function create(Request $request, PaypalManager $manager)
    {
        $cart = Cart::findOrFail($request->cart_id);
        $order = $manager->createOrder($cart);

        return response()->json($order);
    }

    public function authorized(Request $request, PaypalManager $manager) {
        // cart_id, paypal_order_id, paypal_authorization_id
        $cart = Cart::findOrFail($request->cart_id);

        if (!$cart->meta) {
            $cart->update([
                'meta' => [
                    'paypal_authorization_id' => $request->paypal_authorization_id,
                ],
            ]);
        } else {
            $meta = $cart->meta;
            $meta->paypal_authorization_id = $request->paypal_authorization_id;
            $cart->meta = $meta;
            $cart->save();
        }
    }

    public function capture(Request $request, PaypalManager $manager)
    {
        $cart = Cart::findOrFail($request->paypal_order_id);
        $order = $manager->createOrder($cart);

        $payment = Payments::cart($cart)
            ->withData(['paypal_order_id' => $order->id]);

        $payment->authorize();

        return response()->json($order);

    }
}
