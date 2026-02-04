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
     * Test that email is sent when order is placed.
     */
    public function test_sends_email_when_order_is_placed(): void
    {
        Mail::fake();

        // Create required dependencies
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();
        $user = User::factory()->create();

        // Create an order without placed_at (not yet placed)
        $order = Order::factory()->create([
            'placed_at' => null,
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'channel_id' => $channel->id,
        ]);

        // Now update the order to mark it as placed
        $order->update([
            'placed_at' => now(),
        ]);

        // Assert that the email was sent to the configured admin email
        Mail::assertSent(OrderPlacedNotification::class, function ($mail) use ($order) {
            return $mail->hasTo('chasesgirl@live.com') &&
                   $mail->order->id === $order->id;
        });
    }

    /**
     * Test that email is not sent when order is updated but not placed.
     */
    public function test_does_not_send_email_when_order_updated_but_not_placed(): void
    {
        Mail::fake();

        // Create required dependencies
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();
        $user = User::factory()->create();

        // Create an order without placed_at
        $order = Order::factory()->create([
            'placed_at' => null,
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'channel_id' => $channel->id,
        ]);

        // Update the order but don't set placed_at
        $order->update([
            'status' => 'pending',
        ]);

        // Assert that no email was sent
        Mail::assertNotSent(OrderPlacedNotification::class);
    }

    /**
     * Test that email is not sent again when order is updated after being placed.
     */
    public function test_does_not_send_email_twice_for_already_placed_order(): void
    {
        Mail::fake();

        // Create required dependencies
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();
        $user = User::factory()->create();

        // Create an order that is already placed
        $order = Order::factory()->create([
            'placed_at' => now()->subHour(),
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'channel_id' => $channel->id,
        ]);

        // Update the order's status
        $order->update([
            'status' => 'paid',
        ]);

        // Assert that no email was sent
        Mail::assertNotSent(OrderPlacedNotification::class);
    }
}
