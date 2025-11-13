<?php
// TODO: Lost functionality: see diff
namespace App\Livewire;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Collection;
use Lunar\Models\Url;

class Home extends Component
{
    /**
     * The selected category filter.
     *
     * @var string
     */
    public $category = 'All';

    /**
     * The search query.
     *
     * @var string
     */
    public $query = '';

    /**
     * The sort option.
     *
     * @var string
     */
    public $sort = 'featured';

    /**
     * {@inheritDoc}
     */
    protected $queryString = [
        'category' => ['except' => 'All'],
        'query' => ['except' => ''],
        'sort' => ['except' => 'featured'],
    ];

    /**
     * Return all categories.
     *
     * @return array
     */
    public function getCategoriesProperty()
    {
        // Only show categories that have products to avoid empty pages
        // Order by _lft to respect manual sort order set in Lunar admin
        $collections = Collection::whereHas('products')->orderBy('_lft')->get();
        // Use base collection to avoid Eloquent unique() attempting to key by model
        $categories = $collections->toBase()->map(function ($collection) {
            return $collection->translateAttribute('name');
        })->prepend('All')->unique()->values()->toArray();

        return $categories;
    }

    /**
     * Return filtered and sorted products.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsProperty()
    {
        $productsQuery = Product::with([
            'thumbnail',
            'defaultUrl',
            'variants.basePrices.currency',
            'collections'
        ]);

        // Filter by category
        if ($this->category !== 'All') {
            // Get locale and validate it matches expected pattern (2-3 letter codes)
            $locale = app()->getLocale() ?: 'en';
            if (!preg_match('/^[a-z]{2,3}$/i', $locale)) {
                $locale = 'en'; // Fallback to English if locale is invalid
            }

            $productsQuery->whereHas('collections', function ($query) use ($locale) {
                // Lunar stores translatable attributes on the JSON column `attribute_data`
                // Use Laravel's driver-agnostic JSON path syntax
                $query->where("attribute_data->name->value->{$locale}", $this->category);
            });
        }

        $products = $productsQuery->get();

        // Filter by search query
        if (!empty($this->query)) {
            $searchTerm = strtolower($this->query);
            $products = $products->filter(function ($product) use ($searchTerm) {
                $name = strtolower($product->translateAttribute('name'));
                $description = strtolower($product->translateAttribute('description') ?? '');
                return str_contains($name, $searchTerm) || str_contains($description, $searchTerm);
            });
        }

        // Sort products
        switch ($this->sort) {
            case 'price_asc':
                $products = $products->sortBy(function ($product) {
                    return $product->variants->first()?->basePrices->first()?->price->value ?? 0;
                })->values();
                break;
            case 'price_desc':
                $products = $products->sortByDesc(function ($product) {
                    return $product->variants->first()?->basePrices->first()?->price->value ?? 0;
                })->values();
                break;
            default:
                // Featured - keep default order
                break;
        }

        return $products;
    }

    /**
     * Return the sale collection.
     *
     * @return void
     */
    public function getSaleCollectionProperty()
    {
        return Url::whereElementType(Collection::class)->whereSlug('sale')->first()?->element ?? null;
    }

    /**
     * Return all images in sale collection.
     */
    public function getSaleCollectionImagesProperty()
    {
        if (! $this->getSaleCollectionProperty()) {
            return null;
        }

        $collectionProducts = $this->getSaleCollectionProperty()
            ->products()->inRandomOrder()->limit(4)->get();

        $saleImages = $collectionProducts->map(function ($product) {
            return $product->thumbnail;
        });

        return $saleImages->chunk(2);
    }

    /**
     * Return a random collection.
     */
    public function getRandomCollectionProperty(): ?Collection
    {
        $collections = Url::whereElementType((new Collection)->getMorphClass());

        if ($this->getSaleCollectionProperty()) {
            $collections = $collections->where('element_id', '!=', $this->getSaleCollectionProperty()?->id);
        }

        return $collections->inRandomOrder()->first()?->element;
    }

    public function render(): View
    {
        return view('livewire.home');
    }
}
