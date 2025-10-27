<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Collection;
use Lunar\Models\Product;

class CollectionProductSeeder extends Seeder
{
    /**
     * Seed products into collections that currently have none.
     *
     * Idempotent: only acts on empty collections.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $collections = Collection::withCount('products')->get();
            $defaultProducts = Product::orderByDesc('id')->limit(9)->pluck('id');

            foreach ($collections as $collection) {
                if ($collection->products_count > 0) {
                    continue; // Already has products
                }

                if ($defaultProducts->isEmpty()) {
                    continue; // No products to attach
                }

                // Attach a default set of products to this collection
                $collection->products()->syncWithoutDetaching($defaultProducts->all());
            }
        });
    }
}
