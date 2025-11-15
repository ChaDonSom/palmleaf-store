# Lesson: Lunar Polymorphic Relationships

**Date:** November 15, 2025  
**Context:** AddToCartTest failures with MissingCurrencyPriceException  
**Related Files:** `tests/Unit/Http/Livewire/Components/AddToCartTest.php`

## Problem

Tests were failing with `MissingCurrencyPriceException` from Lunar's `PricingManager` even though `Price` records existed in the database. The polymorphic relationship between `ProductVariant` and `Price` was not loading properly.

## Root Cause

Lunar uses **morph maps** to define polymorphic relationship types. When creating `Price` records in tests/factories, using the full class name breaks the relationship:

```php
// ❌ WRONG - This breaks the polymorphic relationship
Price::factory()->create([
    'priceable_type' => ProductVariant::class, // Full class name
    'priceable_id' => $variant->id,
]);
```

This creates a `priceable_type` value like `"App\\Models\\ProductVariant"`, but Lunar's morph map expects `"product_variant"`.

## Solution

Always use `getMorphClass()` instead of `::class` when working with Lunar models in polymorphic relationships:

```php
// ✅ CORRECT - Use getMorphClass()
Price::factory()->create([
    'priceable_type' => $variant->getMorphClass(), // Returns 'product_variant'
    'priceable_id' => $variant->id,
]);
```

After creating related records, refresh the parent model to load the relationship:

```php
$variant->refresh();
$variant->load('prices');
```

## When This Applies

This lesson is critical when:

-   **Writing tests** that create Price, TaxClass, or other polymorphic records for Lunar models
-   **Creating factories** that involve Lunar model relationships
-   **Debugging relationship issues** where records exist in the database but aren't loading via Eloquent
-   **Working with any Lunar polymorphic relationship** (Price→Priceable, Media→Mediable, etc.)

## Affected Lunar Models

Models that commonly use morph maps:

-   `ProductVariant` → `'product_variant'`
-   `Product` → `'product'`
-   `Collection` → `'collection'`
-   `Brand` → `'brand'`

## Key Takeaway

**Never assume `::class` works for polymorphic types in Lunar.** Always use `$model->getMorphClass()` to get the correct morph map string that Lunar expects.

## Technical Details

Lunar registers morph maps in its service provider using Laravel's `Relation::morphMap()`. This allows shorter, more flexible type identifiers instead of full class names. When you use `::class`, you bypass this mapping and create mismatched database records that Laravel's relationship resolver cannot match.

The symptom is usually that:

1. Records exist in the database with correct foreign keys
2. The relationship returns an empty collection
3. Code that depends on the relationship (like pricing calculations) fails

## Reference

-   [Eloquent Polymorphic Relationships](https://laravel.com/docs/eloquent-relationships#polymorphic-relationships)
-   [Morph Maps](https://laravel.com/docs/eloquent-relationships#custom-polymorphic-types)
-   Lunar Source: `vendor/lunarphp/*/src/LunarServiceProvider.php` (morph map registration)
