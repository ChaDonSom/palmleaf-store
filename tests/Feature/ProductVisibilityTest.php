<?php

namespace Tests\Feature;

use App\Livewire\CollectionPage;
use App\Livewire\Home;
use App\Livewire\SearchPage;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Tests\TestCase;

class ProductVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected CustomerGroup $customerGroup;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create([
            'default' => true,
            'code' => 'en',
        ]);

        $this->currency = Currency::factory()->create([
            'default' => true,
        ]);

        $this->channel = Channel::factory()->create([
            'handle' => 'webstore',
            'default' => true,
        ]);

        $this->customerGroup = CustomerGroup::factory()->create([
            'handle' => 'retail',
            'default' => true,
        ]);
    }

    protected function getDefaultLanguage(): Language
    {
        return Language::getDefault();
    }

    /** @test */
    public function draft_products_are_hidden_from_homepage()
    {
        $publishedProduct = $this->createProduct('Published Product', 'published');
        $draftProduct = $this->createProduct('Draft Product', 'draft');

        $component = Livewire::test(Home::class);

        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Published Product', $productNames);
        $this->assertNotContains('Draft Product', $productNames);
    }

    /** @test */
    public function products_switched_to_draft_are_hidden_from_homepage()
    {
        $product = $this->createProduct('Test Product', 'published');

        // First verify it's visible when published
        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();
        $this->assertContains('Test Product', $productNames);

        // Switch to draft
        $product->update(['status' => 'draft']);

        // Verify it's now hidden
        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();
        $this->assertNotContains('Test Product', $productNames);
    }

    /** @test */
    public function products_with_disabled_webstore_channel_are_hidden_from_homepage()
    {
        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $hiddenProduct = $this->createProduct('Hidden Product', 'published');

        // Disable webstore channel for the hidden product
        $hiddenProduct->channels()->updateExistingPivot($this->channel->id, [
            'enabled' => false,
        ]);

        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Hidden Product', $productNames);
    }

    /** @test */
    public function products_with_invisible_retail_customer_group_are_hidden_from_homepage()
    {
        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $hiddenProduct = $this->createProduct('Hidden Product', 'published');

        // Make retail customer group invisible for the hidden product
        $hiddenProduct->customerGroups()->updateExistingPivot($this->customerGroup->id, [
            'visible' => false,
        ]);

        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Hidden Product', $productNames);
    }

    /** @test */
    public function draft_products_are_hidden_from_collection_page()
    {
        $language = $this->getDefaultLanguage();
        $collection = Collection::factory()->create();
        $collection->urls()->create([
            'slug' => 'test-collection',
            'default' => true,
            'language_id' => $language->id,
        ]);

        $publishedProduct = $this->createProduct('Published Product', 'published');
        $draftProduct = $this->createProduct('Draft Product', 'draft');

        $collection->products()->attach([$publishedProduct->id, $draftProduct->id]);

        $component = Livewire::test(CollectionPage::class, ['slug' => 'test-collection']);
        $products = $component->get('collection')->products;
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Published Product', $productNames);
        $this->assertNotContains('Draft Product', $productNames);
    }

    /** @test */
    public function products_with_disabled_webstore_channel_are_hidden_from_collection_page()
    {
        $language = $this->getDefaultLanguage();
        $collection = Collection::factory()->create();
        $collection->urls()->create([
            'slug' => 'test-collection',
            'default' => true,
            'language_id' => $language->id,
        ]);

        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $hiddenProduct = $this->createProduct('Hidden Product', 'published');

        $hiddenProduct->channels()->updateExistingPivot($this->channel->id, [
            'enabled' => false,
        ]);

        $collection->products()->attach([$visibleProduct->id, $hiddenProduct->id]);

        $component = Livewire::test(CollectionPage::class, ['slug' => 'test-collection']);
        $products = $component->get('collection')->products;
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Hidden Product', $productNames);
    }

    /** @test */
    public function products_with_invisible_retail_customer_group_are_hidden_from_collection_page()
    {
        $language = $this->getDefaultLanguage();
        $collection = Collection::factory()->create();
        $collection->urls()->create([
            'slug' => 'test-collection',
            'default' => true,
            'language_id' => $language->id,
        ]);

        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $hiddenProduct = $this->createProduct('Hidden Product', 'published');

        $hiddenProduct->customerGroups()->updateExistingPivot($this->customerGroup->id, [
            'visible' => false,
        ]);

        $collection->products()->attach([$visibleProduct->id, $hiddenProduct->id]);

        $component = Livewire::test(CollectionPage::class, ['slug' => 'test-collection']);
        $products = $component->get('collection')->products;
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Hidden Product', $productNames);
    }

    /** @test */
    public function draft_products_are_hidden_from_search_results()
    {
        // Skip if Scout driver is null (no search engine configured)
        if (config('scout.driver') == 'null' || config('scout.driver') === null) {
            $this->markTestSkipped('Scout search requires a search engine to be configured');
        }

        $publishedProduct = $this->createProduct('Published Product', 'published');
        $draftProduct = $this->createProduct('Draft Product', 'draft');

        $component = Livewire::test(SearchPage::class, ['term' => 'Product']);
        $products = $component->get('results')->getCollection();
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Published Product', $productNames);
        $this->assertNotContains('Draft Product', $productNames);
    }

    /** @test */
    public function products_with_disabled_webstore_channel_are_hidden_from_search_results()
    {
        // Skip if Scout driver is null (no search engine configured)
        if (config('scout.driver') == 'null' || config('scout.driver') === null) {
            $this->markTestSkipped('Scout search requires a search engine to be configured');
        }

        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $hiddenProduct = $this->createProduct('Hidden Product', 'published');

        // Disable webstore channel for the hidden product
        $hiddenProduct->channels()->updateExistingPivot($this->channel->id, [
            'enabled' => false,
        ]);

        $component = Livewire::test(SearchPage::class, ['term' => 'Product']);
        $products = $component->get('results')->getCollection();
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Hidden Product', $productNames);
    }

    /** @test */
    public function products_with_invisible_retail_customer_group_are_hidden_from_search_results()
    {
        // Skip if Scout driver is null (no search engine configured)
        if (config('scout.driver') == 'null' || config('scout.driver') === null) {
            $this->markTestSkipped('Scout search requires a search engine to be configured');
        }

        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $hiddenProduct = $this->createProduct('Hidden Product', 'published');

        // Make retail customer group invisible for the hidden product
        $hiddenProduct->customerGroups()->updateExistingPivot($this->customerGroup->id, [
            'visible' => false,
        ]);

        $component = Livewire::test(SearchPage::class, ['term' => 'Product']);
        $products = $component->get('results')->getCollection();
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Hidden Product', $productNames);
    }

    /** @test */
    public function products_with_future_channel_start_date_are_hidden()
    {
        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $futureProduct = $this->createProduct('Future Product', 'published');

        // Set channel to start in the future
        $futureProduct->channels()->updateExistingPivot($this->channel->id, [
            'starts_at' => now()->addDays(1),
            'enabled' => true,
        ]);

        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Future Product', $productNames);
    }

    /** @test */
    public function products_with_past_channel_end_date_are_hidden()
    {
        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $expiredProduct = $this->createProduct('Expired Product', 'published');

        // Set channel to have ended in the past
        $expiredProduct->channels()->updateExistingPivot($this->channel->id, [
            'ends_at' => now()->subDays(1),
            'enabled' => true,
        ]);

        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Expired Product', $productNames);
    }

    /** @test */
    public function products_with_future_customer_group_start_date_are_hidden()
    {
        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $futureProduct = $this->createProduct('Future Product', 'published');

        // Set customer group to start in the future
        $futureProduct->customerGroups()->updateExistingPivot($this->customerGroup->id, [
            'starts_at' => now()->addDays(1),
            'visible' => true,
        ]);

        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Future Product', $productNames);
    }

    /** @test */
    public function products_with_past_customer_group_end_date_are_hidden()
    {
        $visibleProduct = $this->createProduct('Visible Product', 'published');
        $expiredProduct = $this->createProduct('Expired Product', 'published');

        // Set customer group to have ended in the past
        $expiredProduct->customerGroups()->updateExistingPivot($this->customerGroup->id, [
            'ends_at' => now()->subDays(1),
            'visible' => true,
        ]);

        $component = Livewire::test(Home::class);
        $products = $component->get('products');
        $productNames = $products->map(fn($p) => $p->translateAttribute('name'))->toArray();

        $this->assertContains('Visible Product', $productNames);
        $this->assertNotContains('Expired Product', $productNames);
    }

    protected function createProduct(string $name, string $status): Product
    {
        $productType = ProductType::factory()->create();

        $product = Product::factory()->create([
            'status' => $status,
            'product_type_id' => $productType->id,
            'attribute_data' => [
                'name' => new \Lunar\FieldTypes\TranslatedText([
                    'en' => new \Lunar\FieldTypes\Text($name),
                ]),
            ],
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        Price::factory()->create([
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'currency_id' => $this->currency->id,
        ]);

        return $product->fresh();
    }
}
