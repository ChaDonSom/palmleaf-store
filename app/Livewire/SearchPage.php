<?php

namespace App\Livewire;

use App\Traits\FiltersProductVisibility;
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
    use FiltersProductVisibility;

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
     * 
     * Note: Post-filtering approach may result in pagination inconsistencies
     * where pages may have fewer items than the configured per_page value.
     * This is a limitation of Scout's inability to filter by complex relationships.
     */
    public function getResultsProperty(): LengthAwarePaginator
    {
        // Start with search query and filter by status
        $query = Product::search($this->term)->where('status', 'published');

        // Get results from search engine
        $results = $query->paginate(50);

        // Apply additional filtering for channel and customer group visibility
        // Note: Scout doesn't support complex relationship filtering in the query,
        // so we filter the results after retrieving them
        $filteredItems = $this->filterProductVisibility($results->getCollection());
        $results->setCollection($filteredItems);

        return $results;
    }

    public function render(): View
    {
        return view('livewire.search-page');
    }
}
