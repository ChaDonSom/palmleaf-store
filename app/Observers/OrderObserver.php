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
        // Check if the order was just placed (placed_at was changed from null to a date)
        if ($order->isDirty('placed_at') && $order->placed_at !== null && $order->getOriginal('placed_at') === null) {
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
            Mail::to($adminEmail)->send(new OrderPlacedNotification($order));
        }
    }
}
