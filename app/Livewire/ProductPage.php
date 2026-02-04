<?php

namespace App\Livewire;

use App\Traits\FetchesUrls;
use App\Traits\FiltersProductVisibility;
use App\Traits\GeneratesSuggestedProducts;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductPage extends Component
{
    use FetchesUrls;
    use FiltersProductVisibility;
    use GeneratesSuggestedProducts;

    public ?int $imageId = null;

    /**
     * The selected option values.
     */
    public array $selectedOptionValues = [];

    public function mount($slug): void
    {
        $this->url = $this->fetchUrl(
            $slug,
            (new Product)->getMorphClass(),
            [
                'element.media',
                'element.variants.basePrices.currency',
                'element.variants.basePrices.priceable',
                'element.variants.values.option',
                'element.collections',
            ]
        );

        if (! $this->url) {
            abort(404);
        }

        $this->selectedOptionValues = $this->productOptions->mapWithKeys(function ($data) {
            return [$data['option']->id => $data['values']->first()->id];
        })->toArray();
    }

    /**
     * Reset imageId when variant selection changes.
     */
    public function updatedSelectedOptionValues(): void
    {
        $this->imageId = null;
    }

    /**
     * Computed property to get variant.
     */
    public function getVariantProperty(): ProductVariant
    {
        return $this->product->variants->first(function ($variant) {
            return ! $variant->values->pluck('id')
                ->diff(
                    collect($this->selectedOptionValues)->values()
                )->count();
        });
    }

    /**
     * Computed property to return all available option values.
     */
    public function getProductOptionValuesProperty(): Collection
    {
        return $this->product->variants->pluck('values')->flatten();
    }

    /**
     * Computed propert to get available product options with values.
     */
    public function getProductOptionsProperty(): Collection
    {
        return $this->productOptionValues->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
    }

    /**
     * Computed property to return product.
     */
    public function getProductProperty(): \App\Models\Product
    {
        // Get the product from the URL
        $lunarProduct = $this->url->element;
        
        // If it's already our App\Models\Product, return it
        if ($lunarProduct instanceof \App\Models\Product) {
            return $lunarProduct;
        }
        
        // Otherwise, reload it using our Product model to get access to our custom relationships
        // We need to reload to get the suggestedProducts relationship which is only in our extended model
        return \App\Models\Product::with([
            'media',
            'variants.basePrices.currency',
            'variants.basePrices.priceable',
            'variants.values.option',
            'collections',
        ])->find($lunarProduct->id);
    }

    /**
     * Return all images for the product.
     */
    public function getImagesProperty(): Collection
    {
        return $this->product->media->sortBy('order_column');
    }

    /**
     * Computed property to return current image.
     */
    public function getImageProperty(): ?Media
    {
        if ($this->imageId) {
            return $this->images->firstWhere('id', $this->imageId);
        }

        if (count($this->variant->images)) {
            return $this->variant->images->first();
        }

        if ($primary = $this->images->first(fn($media) => $media->getCustomProperty('primary'))) {
            return $primary;
        }

        return $this->images->first();
    }

    /**
     * Computed property to return suggested products.
     */
    public function getSuggestedProductsProperty(): Collection
    {
        return $this->generateSuggestedProducts($this->product, 4);
    }

    public function render(): View
    {
        return view('livewire.product-page');
    }
}
