<?php

namespace App\Observers;

use Lunar\Models\Order;
use App\Mail\OrderPlacedNotification;
use Illuminate\Support\Facades\Mail;
use Lunar\Admin\Models\Staff;

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
        // Get admin staff ID from config
        $adminStaffId = config('lunar.orders.admin_staff_id');
        $admin = Staff::find($adminStaffId);

        if ($admin) {
            Mail::to($admin->email)->send(new OrderPlacedNotification($order));
        }
    }
}
