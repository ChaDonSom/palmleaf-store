<?php

namespace Database\Seeders;

use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttributeSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributes = $this->getSeedData('attributes');

        $group = AttributeGroup::first();

        DB::transaction(function () use ($attributes, $group) {
            foreach ($attributes as $attribute) {
                $handle = Str::snake($attribute->name);

                Attribute::updateOrCreate(
                    [
                        'attribute_type' => $attribute->attribute_type,
                        'handle' => $handle,
                    ],
                    [
                        'attribute_group_id' => $group->id,
                        'section' => 'main',
                        'type' => $attribute->type,
                        'required' => false,
                        'searchable' => true,
                        'filterable' => false,
                        'system' => false,
                        'position' => $group->attributes()->count() + 1,
                        'name' => [
                            'en' => $attribute->name,
                        ],
                        'configuration' => (array) $attribute->configuration,
                    ]
                );
            }
        });
    }
}
