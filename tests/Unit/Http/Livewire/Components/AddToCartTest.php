<?php

namespace Tests\Unit\Http\Livewire\Components;

use App\Livewire\Components\AddToCart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Tests\TestCase;

class AddToCartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the component mounts correctly.
     *
     * @return void
     */
    public function test_component_can_mount()
    {
        Language::factory()->create([
            'default' => true,
        ]);

        Currency::factory()->create([
            'default' => true,
        ]);

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        Price::factory()->create([
            'priceable_type' => ProductVariant::class,
            'priceable_id' => $variant->id,
        ]);

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
        Language::factory()->create([
            'default' => true,
        ]);

        Currency::factory()->create([
            'default' => true,
        ]);

        $product = Product::factory()->create([
            'attribute_data' => [
                'name' => [
                    'en' => 'Test Product',
                ],
            ],
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        Price::factory()->create([
            'priceable_type' => ProductVariant::class,
            'priceable_id' => $variant->id,
        ]);

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
        Language::factory()->create([
            'default' => true,
        ]);

        Currency::factory()->create([
            'default' => true,
        ]);

        $product = Product::factory()->create([
            'attribute_data' => [
                'name' => [
                    'en' => 'Test Product',
                ],
            ],
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        Price::factory()->create([
            'priceable_type' => ProductVariant::class,
            'priceable_id' => $variant->id,
        ]);

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->set('quantity', 3)
            ->call('addToCart')
            ->assertDispatched('toast', message: '3 × Test Product added to cart');
    }
}
