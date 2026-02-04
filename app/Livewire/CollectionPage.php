<?php
// TODO: Lost functionality: see diff
namespace App\Livewire;

use App\Traits\FetchesUrls;
use App\Traits\FiltersProductVisibility;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Channel;
use Lunar\Models\Collection as CollectionModel;
use Lunar\Models\CustomerGroup;

class CollectionPage extends Component
{
    use FetchesUrls;
    use FiltersProductVisibility;

    public function mount(string $slug): void
    {
        $this->url = $this->fetchUrl(
            $slug,
            (new CollectionModel)->getMorphClass(),
            [
                'element.thumbnail',
                'element.products.variants.basePrices',
                'element.products.defaultUrl',
                'element.products.channels',
                'element.products.customerGroups',
            ]
        );

        if (! $this->url) {
            abort(404);
        }
    }

    /**
     * Computed property to return the collection.
     */
    public function getCollectionProperty(): mixed
    {
        $collection = $this->url->element;

        // Filter products to only show published products with enabled webstore channel
        // and visible retail customer group
        if ($collection) {
            $filteredProducts = $this->filterProductVisibility($collection->products);

            // Replace the products collection with filtered products
            $collection->setRelation('products', $filteredProducts);
        }

        return $collection;
    }

    public function render(): View
    {
        return view('livewire.collection-page');
    }
}
