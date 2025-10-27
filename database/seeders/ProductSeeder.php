<?php

namespace Database\Seeders;

use Database\Seeders\AbstractSeeder;
use Lunar\FieldTypes\ListField;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Hub\Jobs\Products\GenerateVariants;
use Lunar\Models\Attribute;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = $this->getSeedData('products');

        $attributes = Attribute::get();

        $productType = ProductType::first();

        $taxClass = TaxClass::getDefault();

        $currency = Currency::getDefault();

        $collections = Collection::get();

        $language = Language::getDefault();

        DB::transaction(function () use ($products, $attributes, $productType, $taxClass, $currency, $collections, $language) {
            $products->each(function ($product) use ($attributes, $productType, $taxClass, $currency, $collections, $language) {
                $attributeData = [];

                foreach ($product->attributes as $attributeHandle => $value) {
                    $attribute = $attributes->first(fn($att) => $att->handle == $attributeHandle);

                    if ($attribute->type == TranslatedText::class) {
                        $attributeData[$attributeHandle] = new TranslatedText([
                            'en' => new Text($value),
                        ]);
                        continue;
                    }

                    if ($attribute->type == ListField::class) {
                        $attributeData[$attributeHandle] = new ListField((array) $value);
                    }
                }

                // Check if product already exists by SKU
                $existingVariant = ProductVariant::where('sku', $product->sku)->first();

                if ($existingVariant) {
                    // Update existing product
                    $productModel = $existingVariant->product;
                    $productModel->update([
                        'attribute_data' => $attributeData,
                        'status' => 'published',
                    ]);

                    // Update variant
                    $existingVariant->update([
                        'purchasable' => 'always',
                        'shippable' => true,
                        'stock' => 500,
                        'backorder' => 0,
                        'tax_class_id' => $taxClass->id,
                    ]);

                    $variant = $existingVariant;
                } else {
                    // Create new product
                    $productModel = Product::create([
                        'attribute_data' => $attributeData,
                        'product_type_id' => $productType->id,
                        'status' => 'published',
                    ]);

                    $slug = Str::slug($product->attributes->name);

                    $productModel->urls()->create([
                        'default' => true,
                        'slug' => $slug,
                        'language_id' => $language->id,
                    ]);

                    // Only one variant...
                    $variant = ProductVariant::create([
                        'product_id' => $productModel->id,
                        'purchasable' => 'always',
                        'shippable' => true,
                        'stock' => 500,
                        'backorder' => 0,
                        'sku' => $product->sku,
                        'tax_class_id' => $taxClass->id,
                    ]);
                }

                // Update or create price
                Price::updateOrCreate(
                    [
                        'priceable_type' => ProductVariant::class,
                        'priceable_id' => $variant->id,
                        'currency_id' => $currency->id,
                        'tier' => 1,
                    ],
                    [
                        'customer_group_id' => null,
                        'price' => $product->price,
                    ]
                );

                // Add media if it doesn't exist
                if ($productModel->getMedia('images')->isEmpty()) {
                    $media = $productModel->addMedia(
                        base_path("database/seeders/data/images/{$product->image}")
                    )->preservingOriginal()->toMediaCollection('images');

                    $media->setCustomProperty('primary', true);
                    $media->save();
                }

                // Sync collections by slug to ensure reliable matching
                $collectionIds = [];
                $desiredSlugs = collect($product->collections ?? [])->map(fn($c) => strtolower($c))->all();
                $collections->each(function ($coll) use ($desiredSlugs, &$collectionIds) {
                    $slug = optional($coll->defaultUrl)->slug;
                    if ($slug && in_array(strtolower($slug), $desiredSlugs, true)) {
                        $collectionIds[] = $coll->id;
                    }
                });

                if (!empty($collectionIds)) {
                    $productModel->collections()->sync($collectionIds);
                }

                if (! count($product->options ?? [])) {
                    return;
                }

                $options = ProductOption::get();
                $optionValues = ProductOptionValue::get();

                $optionValueIds = [];

                foreach ($product->options ?? [] as $option) {
                    // Do we have this option already?
                    $optionModel = $options->first(fn($opt) => $option->name == $opt->translate('name'));

                    if (! $optionModel) {
                        $optionModel = ProductOption::create([
                            'name' => [
                                'en' => $option->name,
                            ],
                        ]);
                    }

                    foreach ($option->values as $value) {
                        // Does this exist?
                        $valueModel = $optionValues->first(fn($val) => $value == $val->translate('name'));

                        if (! $valueModel) {
                            $valueModel = ProductOptionValue::create([
                                'product_option_id' => $optionModel->id,
                                'name' => [
                                    'en' => $value,
                                ],
                            ]);
                        }

                        $optionValueIds[] = $valueModel->id;
                    }
                }

                // Only generate variants if this is a new product
                if (!$existingVariant) {
                    GenerateVariants::dispatch($productModel, $optionValueIds);
                }
            });
        });
    }
}
