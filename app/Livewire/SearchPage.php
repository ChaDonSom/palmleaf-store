<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Lunar\Models\Channel;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;

class SearchPage extends Component
{
    use WithPagination;

    /**
     * {@inheritDoc}
     */
    protected $queryString = [
        'term',
    ];

    /**
     * The search term.
     */
    public ?string $term = null;

    /**
     * Return the search results.
     */
    public function getResultsProperty(): LengthAwarePaginator
    {
        // Get default channel and customer group for storefront filtering
        $channel = Channel::where('handle', 'webstore')->first();
        $customerGroup = CustomerGroup::where('handle', 'retail')->first();

        // Start with search query
        $query = Product::search($this->term);

        // Apply status filter - only published products
        $query->where('status', 'published');

        // Get results from search engine
        $results = $query->paginate(50);

        // Apply additional filtering for channel and customer group visibility
        // Note: Scout doesn't support complex relationship filtering in the query,
        // so we filter the results after retrieving them
        if ($channel || $customerGroup) {
            $items = $results->getCollection()->filter(function ($product) use ($channel, $customerGroup) {
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
            })->values();

            $results->setCollection($items);
        }

        return $results;
    }

    public function render(): View
    {
        return view('livewire.search-page');
    }
}
