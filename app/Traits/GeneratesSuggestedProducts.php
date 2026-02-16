<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Lunar\Models\Product;

trait GeneratesSuggestedProducts
{
    /**
     * Eager load configuration for suggested products.
     * Ensures products have all necessary relationships loaded for display and pricing.
     */
    private const SUGGESTED_PRODUCT_EAGER_LOAD = [
        'defaultUrl',
        'thumbnail',
        'variants.basePrices.currency',
        'variants.basePrices.priceable',
        'channels',
        'customerGroups',
    ];

    /**
     * Generate suggested products for a given product.
     *
     * @param Product $product The product to find suggestions for
     * @param int $limit Maximum number of suggested products to return
     * @return Collection Collection of suggested Product models
     */
    protected function generateSuggestedProducts(Product $product, int $limit = 4): Collection
    {
        $suggested = collect();

        // 1. First, try to get manually associated suggested products
        $associatedProducts = $product->suggestedProducts()
            ->with(self::SUGGESTED_PRODUCT_EAGER_LOAD)
            ->get();

        $suggested = $suggested->merge($associatedProducts);

        // 2. If we need more, get products from the same collections
        if ($suggested->count() < $limit) {
            $collectionProducts = $this->getProductsFromSameCollections($product, $limit - $suggested->count());
            $suggested = $suggested->merge($collectionProducts);
        }

        // Remove duplicates and the current product itself
        $suggested = $suggested->unique('id')
            ->reject(fn($p) => $p->id === $product->id)
            ->take($limit);

        // Filter by visibility
        return $this->filterProductVisibility($suggested);
    }

    /**
     * Get products from the same collections as the given product.
     *
     * @param Product $product
     * @param int $limit
     * @return Collection
     */
    protected function getProductsFromSameCollections(Product $product, int $limit): Collection
    {
        // Get collection IDs for this product
        $collectionIds = $product->collections->pluck('id');

        if ($collectionIds->isEmpty()) {
            return collect();
        }

        // Find other products in the same collections
        return Product::whereHas('collections', function ($query) use ($collectionIds) {
            $query->whereIn('collection_id', $collectionIds);
        })
            ->where('id', '!=', $product->id)
            ->with(self::SUGGESTED_PRODUCT_EAGER_LOAD)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
