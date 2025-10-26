<?php

namespace App\Http\Livewire;

use Lunar\Models\Collection;
use Lunar\Models\Product;
use Lunar\Models\Url;
use Livewire\Component;

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
        $collections = Collection::all();
        $categories = $collections->map(function ($collection) {
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
            $productsQuery->whereHas('collections', function ($query) {
                $query->whereRaw("json_extract(name, '$.en') = ?", [$this->category]);
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

    public function render()
    {
        return view('livewire.home');
    }
}
