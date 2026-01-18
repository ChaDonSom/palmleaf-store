<?php
// TODO: Lost functionality: see diff
namespace App\Livewire;

use App\Traits\FetchesUrls;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Channel;
use Lunar\Models\Collection as CollectionModel;
use Lunar\Models\CustomerGroup;

class CollectionPage extends Component
{
    use FetchesUrls;

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
        $channel = Channel::where('handle', 'webstore')->first();
        $customerGroup = CustomerGroup::where('handle', 'retail')->first();

        if ($collection && ($channel || $customerGroup)) {
            $filteredProducts = $collection->products->filter(function ($product) use ($channel, $customerGroup) {
                // Check if product is published
                if ($product->status !== 'published') {
                    return false;
                }

                // Check if webstore channel is enabled
                if ($channel) {
                    $channelPivot = $product->channels->firstWhere('id', $channel->id);
                    if (!$channelPivot || !$channelPivot->pivot->enabled) {
                        return false;
                    }

                    // Check channel scheduling
                    $startsAt = $channelPivot->pivot->starts_at;
                    $endsAt = $channelPivot->pivot->ends_at;
                    $now = now();

                    if ($startsAt && $startsAt > $now) {
                        return false;
                    }

                    if ($endsAt && $endsAt < $now) {
                        return false;
                    }
                }

                // Check if retail customer group is visible
                if ($customerGroup) {
                    $customerGroupPivot = $product->customerGroups->firstWhere('id', $customerGroup->id);
                    if (!$customerGroupPivot || !$customerGroupPivot->pivot->visible) {
                        return false;
                    }

                    // Check customer group scheduling
                    $startsAt = $customerGroupPivot->pivot->starts_at;
                    $endsAt = $customerGroupPivot->pivot->ends_at;
                    $now = now();

                    if ($startsAt && $startsAt > $now) {
                        return false;
                    }

                    if ($endsAt && $endsAt < $now) {
                        return false;
                    }
                }

                return true;
            });

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
