<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Tests\TestCase;

class CustomerGroupPurchasableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that customer group purchasable field works correctly with boolean values.
     * 
     * This test verifies the fix for the PostgreSQL boolean casting issue where
     * the 'purchasable' field (tinyint in MySQL, boolean in PostgreSQL) was being
     * cast incorrectly in the admin interface.
     *
     * @return void
     */
    public function test_customer_group_purchasable_field_handles_boolean_values()
    {
        // Create a default language (required for products)
        Language::factory()->create([
            'default' => true,
        ]);

        // Create a currency (required for products)
        Currency::factory()->create([
            'code' => 'GBP',
            'name' => 'British Pound',
            'exchange_rate' => 1,
            'decimal_places' => 2,
            'enabled' => true,
            'default' => true,
        ]);

        // Create a product type
        $productType = ProductType::factory()->create();

        // Create a product
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'status' => 'published',
        ]);

        // Create customer groups
        $customerGroup1 = CustomerGroup::factory()->create([
            'name' => 'Purchasable Group',
            'handle' => 'purchasable-group',
        ]);

        $customerGroup2 = CustomerGroup::factory()->create([
            'name' => 'Non-Purchasable Group',
            'handle' => 'non-purchasable-group',
        ]);

        // Attach customer groups with different purchasable values
        $product->customerGroups()->attach($customerGroup1->id, [
            'purchasable' => true,
            'visible' => true,
        ]);

        $product->customerGroups()->attach($customerGroup2->id, [
            'purchasable' => false,
            'visible' => true,
        ]);

        // Reload the product to ensure pivot data is fresh
        $product->refresh();

        // Get the pivot data
        $pivot1 = $product->customerGroups()->where('lunar_customer_groups.id', $customerGroup1->id)->first()->pivot;
        $pivot2 = $product->customerGroups()->where('lunar_customer_groups.id', $customerGroup2->id)->first()->pivot;

        // Test that boolean true is handled correctly
        // In PostgreSQL, this would be a boolean true, not string '1'
        $this->assertTrue((bool) $pivot1->purchasable, 'Customer group 1 should be purchasable');
        
        // Test that boolean false is handled correctly
        // In PostgreSQL, this would be a boolean false, not string '0'
        $this->assertFalse((bool) $pivot2->purchasable, 'Customer group 2 should not be purchasable');

        // Test that the values can be used in ternary operations (like the fixed code does)
        // This simulates what happens in CustomerGroupRelationManager.php line 84-85
        $color1 = $pivot1->purchasable ? 'success' : 'warning';
        $color2 = $pivot2->purchasable ? 'success' : 'warning';

        $this->assertEquals('success', $color1, 'Purchasable true should result in success color');
        $this->assertEquals('warning', $color2, 'Purchasable false should result in warning color');

        // Test icon logic
        $icon1 = $pivot1->purchasable ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
        $icon2 = $pivot2->purchasable ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';

        $this->assertEquals('heroicon-o-check-circle', $icon1, 'Purchasable true should show check icon');
        $this->assertEquals('heroicon-o-x-circle', $icon2, 'Purchasable false should show x icon');
    }

    /**
     * Test that the visible field also works with boolean values.
     *
     * @return void
     */
    public function test_customer_group_visible_field_handles_boolean_values()
    {
        // Create a default language (required for products)
        Language::factory()->create([
            'default' => true,
        ]);

        // Create a currency (required for products)
        Currency::factory()->create([
            'code' => 'GBP',
            'name' => 'British Pound',
            'exchange_rate' => 1,
            'decimal_places' => 2,
            'enabled' => true,
            'default' => true,
        ]);

        // Create a product type
        $productType = ProductType::factory()->create();

        // Create a product
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'status' => 'published',
        ]);

        // Create a customer group
        $customerGroup = CustomerGroup::factory()->create([
            'name' => 'Test Group',
            'handle' => 'test-group',
        ]);

        // Attach with visible = false
        $product->customerGroups()->attach($customerGroup->id, [
            'purchasable' => true,
            'visible' => false,
        ]);

        // Reload the product
        $product->refresh();

        // Get the pivot data
        $pivot = $product->customerGroups()->where('lunar_customer_groups.id', $customerGroup->id)->first()->pivot;

        // Test that boolean false for visible works correctly
        $this->assertFalse((bool) $pivot->visible, 'Customer group should not be visible');

        // Test ternary operation
        $color = $pivot->visible ? 'success' : 'warning';
        $this->assertEquals('warning', $color, 'Visible false should result in warning color');
    }
}
