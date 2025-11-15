<?php

namespace Tests\Unit\Modifiers;

use App\Modifiers\ShippingModifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\DataTypes\Price;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Tests\TestCase;

/**
 * @group modifiers.shipping
 */
class ShippingModifierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create default language
        Language::factory()->create([
            'default' => true,
            'code' => 'en',
        ]);

        // Create default tax class
        TaxClass::factory()->create([
            'default' => true,
        ]);

        // Disable shipping tables
        config(['shipping-tables.enabled' => false]);
    }

    /**
     * Test shipping is $7.50 for 1 item when cart total is less than $50.
     */
    public function test_shipping_is_750_for_single_item_under_50_dollars()
    {
        $currency = Currency::factory()->create();
        $cart = Cart::factory()->create(['currency_id' => $currency->id]);

        // Add 1 item with price less than $50 (e.g., $30 = 3000 cents)
        $variant = ProductVariant::factory()->create();
        $line = CartLine::factory()->create([
            'cart_id' => $cart->id,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id,
            'quantity' => 1,
        ]);

        // Set line subTotal to $30
        $line->subTotal = new Price(3000, $currency, 1);
        $cart->lines = collect([$line]);

        $modifier = new ShippingModifier();
        $modifier->handle($cart, fn($cart) => $cart);

        $options = ShippingManifest::getOptions($cart);
        $this->assertCount(1, $options);

        $option = $options->first();
        $this->assertEquals(750, $option->price->value); // $7.50
        $this->assertEquals('Standard Shipping', $option->name);
        $this->assertStringContainsString('more for free shipping', $option->description);
    }

    /**
     * Test shipping is $12.00 for 2+ items when cart total is less than $50.
     */
    public function test_shipping_is_1200_for_multiple_items_under_50_dollars()
    {
        $currency = Currency::factory()->create();
        $cart = Cart::factory()->create(['currency_id' => $currency->id]);

        // Add 2 items with total price less than $50
        $variant = ProductVariant::factory()->create();
        $line = CartLine::factory()->create([
            'cart_id' => $cart->id,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id,
            'quantity' => 2,
        ]);

        // Set line subTotal to $40
        $line->subTotal = new Price(4000, $currency, 1);
        $cart->lines = collect([$line]);

        $modifier = new ShippingModifier();
        $modifier->handle($cart, fn($cart) => $cart);

        $options = ShippingManifest::getOptions($cart);
        $this->assertCount(1, $options);

        $option = $options->first();
        $this->assertEquals(1200, $option->price->value); // $12.00
        $this->assertEquals('Standard Shipping', $option->name);
        $this->assertStringContainsString('more for free shipping', $option->description);
    }

    /**
     * Test shipping is free when cart total is $50 or more.
     */
    public function test_shipping_is_free_for_cart_total_50_or_more()
    {
        $currency = Currency::factory()->create();
        $cart = Cart::factory()->create(['currency_id' => $currency->id]);

        // Add item with total $50 or more
        $variant = ProductVariant::factory()->create();
        $line = CartLine::factory()->create([
            'cart_id' => $cart->id,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id,
            'quantity' => 1,
        ]);

        // Set line subTotal to $50
        $line->subTotal = new Price(5000, $currency, 1);
        $cart->lines = collect([$line]);

        $modifier = new ShippingModifier();
        $modifier->handle($cart, fn($cart) => $cart);

        $options = ShippingManifest::getOptions($cart);
        $this->assertCount(1, $options);

        $option = $options->first();
        $this->assertEquals(0, $option->price->value); // Free shipping
        $this->assertEquals('Free Shipping', $option->description);
    }

    /**
     * Test shipping is free when cart total is over $50.
     */
    public function test_shipping_is_free_for_cart_total_over_50()
    {
        $currency = Currency::factory()->create();
        $cart = Cart::factory()->create(['currency_id' => $currency->id]);

        // Add item with total over $50
        $variant = ProductVariant::factory()->create();
        $line = CartLine::factory()->create([
            'cart_id' => $cart->id,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id,
            'quantity' => 2,
        ]);

        // Set line subTotal to $75
        $line->subTotal = new Price(7500, $currency, 1);
        $cart->lines = collect([$line]);

        $modifier = new ShippingModifier();
        $modifier->handle($cart, fn($cart) => $cart);

        $options = ShippingManifest::getOptions($cart);
        $this->assertCount(1, $options);

        $option = $options->first();
        $this->assertEquals(0, $option->price->value); // Free shipping
        $this->assertEquals('Free Shipping', $option->description);
    }

    /**
     * Test shipping with multiple cart lines totaling 2+ items.
     */
    public function test_shipping_with_multiple_cart_lines()
    {
        $currency = Currency::factory()->create();
        $cart = Cart::factory()->create(['currency_id' => $currency->id]);

        // Add 2 different items (1 each) = 2 total items
        $variant1 = ProductVariant::factory()->create();
        $variant2 = ProductVariant::factory()->create();

        $line1 = CartLine::factory()->create([
            'cart_id' => $cart->id,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant1->id,
            'quantity' => 1,
        ]);

        $line2 = CartLine::factory()->create([
            'cart_id' => $cart->id,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant2->id,
            'quantity' => 1,
        ]);

        // Set line subTotals to total $40 ($20 each)
        $line1->subTotal = new Price(2000, $currency, 1);
        $line2->subTotal = new Price(2000, $currency, 1);
        $cart->lines = collect([$line1, $line2]);

        $modifier = new ShippingModifier();
        $modifier->handle($cart, fn($cart) => $cart);

        $options = ShippingManifest::getOptions($cart);
        $this->assertCount(1, $options);

        $option = $options->first();
        $this->assertEquals(1200, $option->price->value); // $12.00 for 2+ items
    }
}
