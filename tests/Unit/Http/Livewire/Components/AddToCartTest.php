<?php

namespace Tests\Unit\Http\Livewire\Components;

use App\Livewire\Components\AddToCart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Facades\CartSession;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Tests\TestCase;

class AddToCartTest extends TestCase
{
    use RefreshDatabase;

    protected Currency $currency;
    protected Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create([
            'default' => true,
        ]);

        $this->currency = Currency::factory()->create([
            'default' => true,
        ]);

        $this->channel = Channel::factory()->create([
            'default' => true,
        ]);
    }

    /**
     * Test the component mounts correctly.
     *
     * @return void
     */
    public function test_component_can_mount()
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        Price::factory()->create([
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'currency_id' => $this->currency->id,
        ]);

        // Refresh the variant to load the price relationship
        $variant->refresh();
        $variant->load('prices');

        // Ensure cart session uses our seeded defaults
        CartSession::setCurrency($this->currency);
        CartSession::setChannel($this->channel);

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->assertViewIs('livewire.components.add-to-cart');
    }

    /**
     * Test adding to cart dispatches toast event.
     *
     * @return void
     */
    public function test_add_to_cart_dispatches_toast_event()
    {
        $product = Product::factory()->create();

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        Price::factory()->create([
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'currency_id' => $this->currency->id,
        ]);

        // Refresh the variant to load the price relationship
        $variant->refresh();
        $variant->load('prices');

        // Initialize cart session with proper currency and channel
        CartSession::setCurrency($this->currency);
        CartSession::setChannel($this->channel);
        $cart = CartSession::manager(); // Force cart creation with proper currency/channel

        // Set session for Livewire test
        session(['lunar_cart' => $cart->id]);

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->call('addToCart')
            ->assertDispatched('toast');
    }

    /**
     * Test adding multiple items to cart shows correct quantity in toast.
     *
     * @return void
     */
    public function test_add_to_cart_with_quantity_dispatches_correct_message()
    {
        $product = Product::factory()->create();

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        Price::factory()->create([
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'currency_id' => $this->currency->id,
        ]);

        // Refresh the variant to load the price relationship
        $variant->refresh();
        $variant->load('prices');

        // Initialize cart session with proper currency and channel
        CartSession::setCurrency($this->currency);
        CartSession::setChannel($this->channel);
        $cart = CartSession::manager(); // Force cart creation with proper currency/channel

        $expectedName = $variant->product->translateAttribute('name');

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->set('quantity', 3)
            ->call('addToCart')
            ->assertDispatched('toast', message: "3 × {$expectedName} added to cart");
    }
}
