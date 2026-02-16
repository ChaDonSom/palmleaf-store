<?php

namespace App\Observers;

use Lunar\Models\Order;
use App\Mail\OrderPlacedNotification;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if the order status was changed to 'requires-capture' (for manual capture)
        // This happens before payment is captured, to notify admin of pending orders
        if ($order->isDirty('status') && $order->status === 'requires-capture' && $order->getOriginal('status') !== 'requires-capture') {
            $this->sendOrderPlacedNotification($order);
        }
    }

    /**
     * Send order placed notification to admin.
     */
    protected function sendOrderPlacedNotification(Order $order): void
    {
        // Get admin email from config
        $adminEmail = config('lunar.orders.admin_email');

        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new OrderPlacedNotification($order));
        }
    }
}
