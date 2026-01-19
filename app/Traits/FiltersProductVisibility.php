<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Lunar\Models\Channel;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;

trait FiltersProductVisibility
{
    /**
     * Filter products by status, channel, and customer group visibility.
     *
     * @param Collection $products Collection of Product models
     * @param Channel|null $channel Channel to filter by (defaults to 'webstore')
     * @param CustomerGroup|null $customerGroup Customer group to filter by (defaults to 'retail')
     * @return Collection Filtered collection of products
     */
    protected function filterProductVisibility(Collection $products, ?Channel $channel = null, ?CustomerGroup $customerGroup = null): Collection
    {
        // Get default channel and customer group if not provided
        // Note: These queries could be cached if performance becomes an issue
        if ($channel === null) {
            $channel = Channel::where('handle', 'webstore')->first();
            if (!$channel) {
                \Log::warning('Webstore channel not found. Products may not be filtered correctly.');
            }
        }

        if ($customerGroup === null) {
            $customerGroup = CustomerGroup::where('handle', 'retail')->first();
            if (!$customerGroup) {
                \Log::warning('Retail customer group not found. Products may not be filtered correctly.');
            }
        }

        // Cache the current time to avoid multiple calls
        $now = now();

        return $products->filter(function ($product) use ($channel, $customerGroup, $now) {
            // Check if product is published
            if ($product->status !== 'published') {
                return false;
            }

            // Check if webstore channel is enabled
            // Note: Products must be explicitly attached to the webstore channel to be visible
            if ($channel) {
                $channelPivot = $product->channels->firstWhere('id', $channel->id);
                if (!$channelPivot || !$channelPivot->pivot->enabled) {
                    return false;
                }

                // Check channel scheduling
                $startsAt = $channelPivot->pivot->starts_at;
                $endsAt = $channelPivot->pivot->ends_at;

                if ($startsAt && $startsAt > $now) {
                    return false;
                }

                if ($endsAt && $endsAt < $now) {
                    return false;
                }
            }

            // Check if retail customer group is visible
            // Note: Products must be explicitly attached to the retail customer group to be visible
            if ($customerGroup) {
                $customerGroupPivot = $product->customerGroups->firstWhere('id', $customerGroup->id);
                if (!$customerGroupPivot || !$customerGroupPivot->pivot->visible) {
                    return false;
                }

                // Check customer group scheduling
                $startsAt = $customerGroupPivot->pivot->starts_at;
                $endsAt = $customerGroupPivot->pivot->ends_at;

                if ($startsAt && $startsAt > $now) {
                    return false;
                }

                if ($endsAt && $endsAt < $now) {
                    return false;
                }
            }

            return true;
        })->values();
    }
}
