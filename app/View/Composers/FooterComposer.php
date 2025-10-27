<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Lunar\Models\Collection;

class FooterComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Cache footer collections for 1 hour to avoid N+1 queries on every page render
        $footerCollections = Cache::remember('footer_collections', 3600, function () {
            return Collection::with('defaultUrl')
                ->limit(4)
                ->get();
        });

        $view->with('footerCollections', $footerCollections);
    }
}
