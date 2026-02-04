<?php

namespace App\Livewire;

use App\Traits\FiltersProductVisibility;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

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
     * Note: Post-filtering approach corrects pagination counts to reflect
     * only visible items after filtering.
     */
    public function getResultsProperty(): LengthAwarePaginator
    {
        // Start with search query and filter by status
        $query = Product::search($this->term)->where('status', 'published');

        // Get results from search engine
        $results = $query->paginate(50);

        // Eager load relationships required for visibility filtering to avoid N+1 queries
        $products = $results->getCollection();
        $products->load(['channels', 'customerGroups']);

        // Apply additional filtering for channel and customer group visibility
        // Note: Scout doesn't support complex relationship filtering in the query,
        // so we filter the results after retrieving them
        $filteredItems = $this->filterProductVisibility($products);

        // Rebuild paginator with correct total count to match filtered items
        return new LengthAwarePaginator(
            $filteredItems,
            $filteredItems->count(),
            $results->perPage(),
            $results->currentPage(),
            [
                'path' => $results->path(),
                'query' => request()->query(),
            ]
        );
    }

    public function render(): View
    {
        return view('livewire.search-page');
    }
}
