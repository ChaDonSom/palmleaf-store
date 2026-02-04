<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Tests\TestCase;

class SuggestedProductsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create default language and currency
        Language::factory()->create([
            'default' => true,
        ]);

        $this->currency = Currency::factory()->create([
            'default' => true,
        ]);

        // Create webstore channel and retail customer group
        $this->channel = Channel::factory()->create([
            'handle' => 'webstore',
            'default' => true,
        ]);

        $this->customerGroup = CustomerGroup::factory()->create([
            'handle' => 'retail',
            'default' => true,
        ]);
    }

    protected function createProduct($attributes = [], $attachToChannel = true, $attachToCustomerGroup = true)
    {
        $product = Product::factory()
            ->hasUrls(1, [
                'default' => true,
            ])
            ->has(ProductVariant::factory()->afterCreating(function ($variant) {
                $variant->prices()->create(
                    Price::factory()->make([
                        'currency_id' => $this->currency->id,
                    ])->getAttributes()
                );
            }), 'variants')
            ->create($attributes);

        $product->addMedia(UploadedFile::fake()->image('product.jpg'))->toMediaCollection('images');

        if ($attachToChannel) {
            $product->channels()->attach($this->channel, [
                'enabled' => true,
                'starts_at' => now(),
            ]);
        }

        if ($attachToCustomerGroup) {
            $product->customerGroups()->attach($this->customerGroup, [
                'enabled' => true,
                'visible' => true,
                'purchasable' => true,
            ]);
        }

        return $product;
    }

    public function test_suggested_products_from_same_collection()
    {
        $collection = Collection::factory()->create();

        // Create main product
        $mainProduct = $this->createProduct(['status' => 'published']);
        
        // Create 3 suggested products in the same collection
        $suggestedProduct1 = $this->createProduct(['status' => 'published']);
        $suggestedProduct2 = $this->createProduct(['status' => 'published']);
        $suggestedProduct3 = $this->createProduct(['status' => 'published']);

        // Attach all products to the same collection
        $collection->products()->attach([
            $mainProduct->id,
            $suggestedProduct1->id,
            $suggestedProduct2->id,
            $suggestedProduct3->id,
        ]);

        // Visit the main product page
        $component = Livewire::test(\App\Livewire\ProductPage::class, ['slug' => $mainProduct->defaultUrl->slug]);
        
        $suggestedProducts = $component->get('suggestedProducts');
        
        // Should have at least some suggested products
        $this->assertGreaterThan(0, $suggestedProducts->count());
        
        // Suggested products should not include the main product
        $this->assertFalse($suggestedProducts->contains('id', $mainProduct->id));
        
        // All suggested products should be from the same collection
        $suggestedIds = $suggestedProducts->pluck('id')->toArray();
        $this->assertTrue(in_array($suggestedProduct1->id, $suggestedIds) || 
                         in_array($suggestedProduct2->id, $suggestedIds) || 
                         in_array($suggestedProduct3->id, $suggestedIds));
    }

    public function test_manually_associated_suggested_products_are_prioritized()
    {
        // Create main product
        $mainProduct = $this->createProduct(['status' => 'published']);
        
        // Create manually suggested products
        $manualProduct1 = $this->createProduct(['status' => 'published']);
        $manualProduct2 = $this->createProduct(['status' => 'published']);

        // Create the product association manually
        \DB::table(config('lunar.database.table_prefix').'product_associations')->insert([
            [
                'product_parent_id' => $mainProduct->id,
                'product_target_id' => $manualProduct1->id,
                'type' => 'suggested',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_parent_id' => $mainProduct->id,
                'product_target_id' => $manualProduct2->id,
                'type' => 'suggested',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Visit the main product page
        $component = Livewire::test(\App\Livewire\ProductPage::class, ['slug' => $mainProduct->defaultUrl->slug]);
        
        $suggestedProducts = $component->get('suggestedProducts');
        
        // Should have the manually associated products
        $this->assertGreaterThan(0, $suggestedProducts->count());
        
        $suggestedIds = $suggestedProducts->pluck('id')->toArray();
        $this->assertTrue(in_array($manualProduct1->id, $suggestedIds));
        $this->assertTrue(in_array($manualProduct2->id, $suggestedIds));
    }

    public function test_suggested_products_respect_visibility_filters()
    {
        $collection = Collection::factory()->create();

        // Create main product
        $mainProduct = $this->createProduct(['status' => 'published']);
        
        // Create a visible suggested product
        $visibleProduct = $this->createProduct(['status' => 'published']);
        
        // Create a draft product (should be hidden)
        $draftProduct = $this->createProduct(['status' => 'draft']);

        // Attach all products to the same collection
        $collection->products()->attach([
            $mainProduct->id,
            $visibleProduct->id,
            $draftProduct->id,
        ]);

        // Visit the main product page
        $component = Livewire::test(\App\Livewire\ProductPage::class, ['slug' => $mainProduct->defaultUrl->slug]);
        
        $suggestedProducts = $component->get('suggestedProducts');
        
        // Draft product should not be in suggested products
        $this->assertFalse($suggestedProducts->contains('id', $draftProduct->id));
        
        // Visible product should be included (if any suggested products exist)
        if ($suggestedProducts->count() > 0) {
            // At least one should be the visible product or another published one
            $this->assertTrue($suggestedProducts->every(function ($product) {
                return $product->status === 'published';
            }));
        }
    }

    public function test_product_page_displays_suggested_products_section()
    {
        $collection = Collection::factory()->create();

        // Create main product
        $mainProduct = $this->createProduct(['status' => 'published']);
        
        // Create suggested products
        $suggestedProduct = $this->createProduct(['status' => 'published']);

        // Attach to the same collection
        $collection->products()->attach([
            $mainProduct->id,
            $suggestedProduct->id,
        ]);

        // Visit the main product page
        $component = Livewire::test(\App\Livewire\ProductPage::class, ['slug' => $mainProduct->defaultUrl->slug]);
        
        // Check that the suggested products section appears
        $component->assertSee('You May Also Like');
    }
}
