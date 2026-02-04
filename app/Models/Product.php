<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Models\Product as LunarProduct;

class Product extends LunarProduct
{
    /**
     * Get suggested products associated with this product.
     */
    public function suggestedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            config('lunar.database.table_prefix').'product_associations',
            'product_parent_id',
            'product_target_id'
        )->wherePivot('type', 'suggested');
    }
}
