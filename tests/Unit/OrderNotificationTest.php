<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Mail\OrderPlacedNotification;
use Lunar\Models\Order;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use App\Models\User;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set the admin email in config
        Config::set('lunar.orders.admin_email', 'chasesgirl@live.com');
    }

    /**
     * Test that email is sent when order status changes to 'requires-capture'.
     */
    public function test_sends_email_when_order_requires_capture(): void
    {
        Mail::fake();

        // Create required dependencies
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();
        $user = User::factory()->create();

        // Create an order with initial status (e.g., 'awaiting-payment')
        $order = Order::factory()->create([
            'status' => 'awaiting-payment',
            'placed_at' => null,
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'channel_id' => $channel->id,
        ]);

        // Update the order status to 'requires-capture' (manual capture mode)
        $order->update([
            'status' => 'requires-capture',
        ]);

        // Assert that the email was sent to the configured admin email
        Mail::assertSent(OrderPlacedNotification::class, function ($mail) use ($order) {
            return $mail->hasTo('chasesgirl@live.com') &&
                   $mail->order->id === $order->id;
        });
    }

    /**
     * Test that email is not sent when order is updated to a different status.
     */
    public function test_does_not_send_email_for_other_status_updates(): void
    {
        Mail::fake();

        // Create required dependencies
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();
        $user = User::factory()->create();

        // Create an order with initial status
        $order = Order::factory()->create([
            'status' => 'awaiting-payment',
            'placed_at' => null,
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'channel_id' => $channel->id,
        ]);

        // Update the order to a different status (not requires-capture)
        $order->update([
            'status' => 'pending',
        ]);

        // Assert that no email was sent
        Mail::assertNotSent(OrderPlacedNotification::class);
    }

    /**
     * Test that email is not sent again when order is updated after already being in requires-capture status.
     */
    public function test_does_not_send_email_twice_for_already_captured_order(): void
    {
        Mail::fake();

        // Create required dependencies
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();
        $user = User::factory()->create();

        // Create an order that already has requires-capture status
        $order = Order::factory()->create([
            'status' => 'requires-capture',
            'placed_at' => null,
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'channel_id' => $channel->id,
        ]);

        // Update something else on the order (e.g., notes or other field)
        $order->update([
            'notes' => 'Some notes',
        ]);

        // Assert that no email was sent
        Mail::assertNotSent(OrderPlacedNotification::class);
    }

    /**
     * Test that email is not sent when order transitions from requires-capture to paid.
     */
    public function test_does_not_send_email_when_transitioning_to_paid(): void
    {
        Mail::fake();

        // Create required dependencies
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();
        $user = User::factory()->create();

        // Create an order that has requires-capture status
        $order = Order::factory()->create([
            'status' => 'requires-capture',
            'placed_at' => null,
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'channel_id' => $channel->id,
        ]);

        // Clear any emails sent during creation
        Mail::fake();

        // Update the order status to 'paid' (after manual capture)
        $order->update([
            'status' => 'paid',
            'placed_at' => now(),
        ]);

        // Assert that no email was sent for this transition
        Mail::assertNotSent(OrderPlacedNotification::class);
    }
}
