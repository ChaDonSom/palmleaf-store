<?php

namespace Tests\Feature;

use Filament\Tables\Columns\IconColumn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Admin\Filament\Resources\ProductResource\RelationManagers\CustomerGroupRelationManager;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Tests\TestCase;

class LunarPostgresFixTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Lunar's CustomerGroupRelationManager correctly handles boolean values.
     * 
     * This test verifies that the installed Lunar package includes the fix for the
     * PostgreSQL boolean casting issue by actually calling the column definitions
     * with boolean values and ensuring no UnhandledMatchError is thrown.
     * 
     * THE ISSUE:
     * - PostgreSQL casts tinyint/boolean columns to PHP bool (true/false)
     * - Old Lunar code used: match ($state) { '1' => 'success', '0' => 'warning', }
     * - When boolean false is cast to string, it becomes '' (empty string), not '0'
     * - This caused UnhandledMatchError: "Unhandled match case ''" in the admin interface
     * 
     * THE FIX (merged Nov 11, 2025 in PR #2330):
     * - Changed to: $state ? 'success' : 'warning'
     * - This works with boolean, integer, and string values
     * 
     * This test verifies the fix by calling the actual Lunar column callbacks.
     *
     * @return void
     */
    public function test_lunar_customer_group_columns_handle_boolean_values()
    {
        // Create required dependencies
        Language::factory()->create(['default' => true]);
        Currency::factory()->create([
            'code' => 'GBP',
            'exchange_rate' => 1,
            'enabled' => true,
            'default' => true,
        ]);

        // Create product and customer group
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'status' => 'published',
        ]);

        $customerGroup = CustomerGroup::factory()->create([
            'name' => 'Test Group',
            'handle' => 'test-group',
        ]);

        // Attach with boolean false (the critical case for PostgreSQL)
        $product->customerGroups()->attach($customerGroup->id, [
            'purchasable' => false,
            'visible' => false,
        ]);

        // Instantiate the actual Lunar CustomerGroupRelationManager
        $relationManager = new CustomerGroupRelationManager();
        $relationManager->ownerRecord = $product;
        
        // Get the table definition from Lunar - this is what the admin UI uses
        $table = $relationManager->getDefaultTable(new \Filament\Tables\Table($relationManager));
        
        // Get the columns that Lunar defines
        $columns = $table->getColumns();
        
        // Find the purchasable and visible columns
        $purchasableColumn = collect($columns)->first(fn ($col) => $col->getName() === 'purchasable');
        $visibleColumn = collect($columns)->first(fn ($col) => $col->getName() === 'visible');
        
        // Verify these are IconColumns (as expected)
        $this->assertInstanceOf(IconColumn::class, $purchasableColumn);
        $this->assertInstanceOf(IconColumn::class, $visibleColumn);
        
        // **THIS IS THE KEY TEST**
        // Call the actual color and icon methods from Lunar with boolean values.
        // If the old buggy match statement code is present, this will throw UnhandledMatchError
        // because boolean false cast to string is '', not '0'.
        // With the fix (ternary operators), this works correctly.
        
        // Test with boolean false - this is what PostgreSQL would pass
        // This would throw "Unhandled match case ''" with the old code
        $falseColor = $purchasableColumn->getColor(false);
        $falseIcon = $purchasableColumn->getIcon(false);
        
        $this->assertEquals('warning', $falseColor, 
            'Boolean false should return warning color without throwing error');
        $this->assertEquals('heroicon-o-x-circle', $falseIcon, 
            'Boolean false should return x-circle icon without throwing error');
        
        // Test with boolean true
        $trueColor = $purchasableColumn->getColor(true);
        $trueIcon = $purchasableColumn->getIcon(true);
        
        $this->assertEquals('success', $trueColor, 
            'Boolean true should return success color');
        $this->assertEquals('heroicon-o-check-circle', $trueIcon, 
            'Boolean true should return check-circle icon');
        
        // Also test with integer values (MySQL behavior)
        $oneColor = $purchasableColumn->getColor(1);
        $zeroColor = $purchasableColumn->getColor(0);
        
        $this->assertEquals('success', $oneColor, 
            'Integer 1 should return success color');
        $this->assertEquals('warning', $zeroColor, 
            'Integer 0 should return warning color');
    }

    /**
     * Test that boolean values from the database work with Lunar's columns.
     * 
     * This test retrieves actual database pivot data (which PostgreSQL would return
     * as boolean values) and verifies Lunar's column callbacks handle them correctly.
     *
     * @return void
     */
    public function test_lunar_columns_handle_database_boolean_values()
    {
        // Create required dependencies
        Language::factory()->create(['default' => true]);
        Currency::factory()->create([
            'code' => 'GBP',
            'exchange_rate' => 1,
            'enabled' => true,
            'default' => true,
        ]);

        // Create product and customer groups
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'status' => 'published',
        ]);

        $purchasableGroup = CustomerGroup::factory()->create([
            'name' => 'Purchasable Group',
            'handle' => 'purchasable-group',
        ]);

        $nonPurchasableGroup = CustomerGroup::factory()->create([
            'name' => 'Non-Purchasable Group',
            'handle' => 'non-purchasable-group',
        ]);

        // Attach with different boolean values
        $product->customerGroups()->attach($purchasableGroup->id, [
            'purchasable' => true,
            'visible' => true,
        ]);

        $product->customerGroups()->attach($nonPurchasableGroup->id, [
            'purchasable' => false,
            'visible' => false,
        ]);

        // Get pivot data from database (PostgreSQL would return boolean values)
        $product->refresh();
        $pivot1 = $product->customerGroups()
            ->where('lunar_customer_groups.id', $purchasableGroup->id)
            ->first()
            ->pivot;
        $pivot2 = $product->customerGroups()
            ->where('lunar_customer_groups.id', $nonPurchasableGroup->id)
            ->first()
            ->pivot;

        // Get Lunar's column definitions
        $relationManager = new CustomerGroupRelationManager();
        $relationManager->ownerRecord = $product;
        $table = $relationManager->getDefaultTable(new \Filament\Tables\Table($relationManager));
        $columns = $table->getColumns();
        $purchasableColumn = collect($columns)->first(fn ($col) => $col->getName() === 'purchasable');

        // **CRITICAL TEST**: Use actual database values with Lunar's column methods
        // If the fix is not present, passing boolean false from database would throw error
        
        $color1 = $purchasableColumn->getColor($pivot1->purchasable);
        $icon1 = $purchasableColumn->getIcon($pivot1->purchasable);
        $color2 = $purchasableColumn->getColor($pivot2->purchasable);
        $icon2 = $purchasableColumn->getIcon($pivot2->purchasable);

        $this->assertEquals('success', $color1, 
            'Database true value should work with Lunar callbacks');
        $this->assertEquals('heroicon-o-check-circle', $icon1, 
            'Database true value should work with Lunar callbacks');
        $this->assertEquals('warning', $color2, 
            'Database false value should work with Lunar callbacks (THE FIX)');
        $this->assertEquals('heroicon-o-x-circle', $icon2, 
            'Database false value should work with Lunar callbacks (THE FIX)');
    }
}
