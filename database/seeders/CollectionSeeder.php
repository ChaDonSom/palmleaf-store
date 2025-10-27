<?php

namespace Database\Seeders;

use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Language;
use Lunar\Models\Url;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CollectionSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $collections = $this->getSeedData('collections');

        $defaultLanguage = Language::getDefault();

        DB::transaction(function () use ($collections, $defaultLanguage) {
            foreach ($collections as $collection) {
                $slug = Str::slug($collection->name);

                // Check if collection already exists by finding a URL with this slug
                $existingUrl = Url::where('slug', $slug)
                    ->where('element_type', Collection::class)
                    ->first();

                if ($existingUrl) {
                    // Update existing collection
                    $model = Collection::find($existingUrl->element_id);
                    $model->update([
                        'attribute_data' =>  [
                            'name' => new TranslatedText([
                                'en' => new Text($collection->name),
                            ]),
                            'description' => new TranslatedText([
                                'en' => new Text($collection->description),
                            ]),
                        ],
                    ]);
                } else {
                    // Create new collection
                    $model = Collection::create([
                        'collection_group_id' => CollectionGroup::first()->id,
                        'attribute_data' =>  [
                            'name' => new TranslatedText([
                                'en' => new Text($collection->name),
                            ]),
                            'description' => new TranslatedText([
                                'en' => new Text($collection->description),
                            ]),
                        ],
                    ]);

                    $model->urls()->create([
                        'slug' => $slug,
                        'default' => true,
                        'language_id' => $defaultLanguage->id,
                    ]);
                }
            }
        });
    }
}
