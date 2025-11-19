<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Admin\Filament\Resources\ProductResource\RelationManagers\CustomerGroupRelationManager;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use ReflectionClass;
use Tests\TestCase;

class LunarPostgresFixTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Lunar's CustomerGroupRelationManager has the PostgreSQL boolean fix.
     * 
     * This test verifies that the installed Lunar package includes the fix for the
     * PostgreSQL boolean casting issue. The test checks that the code uses ternary
     * operators (not match statements) which work with boolean values.
     * 
     * THE ISSUE:
     * - PostgreSQL casts tinyint/boolean columns to PHP bool (true/false)
     * - Old Lunar code used: match ($state) { '1' => 'success', '0' => 'warning', }
     * - When boolean false is cast to string, it becomes '' (empty string), not '0'
     * - This caused UnhandledMatchError in the admin interface
     * 
     * THE FIX (merged Nov 11, 2025 in PR #2330):
     * - Changed to: $state ? 'success' : 'warning'
     * - This works with boolean, integer, and string values
     * 
     * This test verifies the fix is present by inspecting the actual Lunar code.
     *
     * @return void
     */
    public function test_lunar_has_postgres_boolean_casting_fix()
    {
        // Read the actual Lunar CustomerGroupRelationManager source code
        $reflection = new ReflectionClass(CustomerGroupRelationManager::class);
        $fileName = $reflection->getFileName();
        $sourceCode = file_get_contents($fileName);
        
        // Verify the fix is present: should use ternary operators, not match statements
        $hasTernaryOperator = str_contains($sourceCode, '->color(fn ($state): string => $state ? \'success\' : \'warning\')');
        $hasIconTernary = str_contains($sourceCode, '->icon(fn ($state): string => $state ? \'heroicon-o-check-circle\' : \'heroicon-o-x-circle\')');
        
        // Verify the OLD buggy code is NOT present
        $hasOldMatchStatement = str_contains($sourceCode, 'match ($state)') && 
                                str_contains($sourceCode, '\'1\' => \'success\'') &&
                                str_contains($sourceCode, '\'0\' => \'warning\'');
        
        $this->assertTrue($hasTernaryOperator, 
            'Lunar CustomerGroupRelationManager should use ternary operator for color (the fix)');
        $this->assertTrue($hasIconTernary, 
            'Lunar CustomerGroupRelationManager should use ternary operator for icon (the fix)');
        $this->assertFalse($hasOldMatchStatement, 
            'Lunar CustomerGroupRelationManager should NOT have the old match statement (the bug)');
    }

    /**
     * Test that boolean values work correctly with the database.
     * 
     * This test creates actual database records and verifies that boolean
     * purchasable/visible values are stored and retrieved correctly, simulating
     * what PostgreSQL would do.
     *
     * @return void
     */
    public function test_boolean_values_work_in_database()
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

        // Test with boolean false (this is the critical case for PostgreSQL)
        $product->customerGroups()->attach($customerGroup->id, [
            'purchasable' => false,  // Boolean false
            'visible' => false,
        ]);

        // Retrieve the pivot data
        $product->refresh();
        $pivot = $product->customerGroups()
            ->where('lunar_customer_groups.id', $customerGroup->id)
            ->first()
            ->pivot;

        // Verify the values are stored correctly
        // In PostgreSQL, these would be actual boolean values, not strings
        $this->assertFalse((bool) $pivot->purchasable, 
            'Purchasable false should be stored and retrieved correctly');
        $this->assertFalse((bool) $pivot->visible, 
            'Visible false should be stored and retrieved correctly');

        // Verify the ternary operator logic works (simulating what Lunar's code does)
        $color = $pivot->purchasable ? 'success' : 'warning';
        $icon = $pivot->purchasable ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';

        $this->assertEquals('warning', $color, 
            'Ternary operator should work with boolean false from database');
        $this->assertEquals('heroicon-o-x-circle', $icon, 
            'Ternary operator should work with boolean false from database');

        // Test with boolean true
        $product->customerGroups()->detach($customerGroup->id);
        $product->customerGroups()->attach($customerGroup->id, [
            'purchasable' => true,  // Boolean true
            'visible' => true,
        ]);

        $product->refresh();
        $pivot = $product->customerGroups()
            ->where('lunar_customer_groups.id', $customerGroup->id)
            ->first()
            ->pivot;

        $this->assertTrue((bool) $pivot->purchasable, 
            'Purchasable true should be stored and retrieved correctly');

        $color = $pivot->purchasable ? 'success' : 'warning';
        $icon = $pivot->purchasable ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';

        $this->assertEquals('success', $color, 
            'Ternary operator should work with boolean true from database');
        $this->assertEquals('heroicon-o-check-circle', $icon, 
            'Ternary operator should work with boolean true from database');
    }

    /**
     * Test that the ternary operator fix works with various input types.
     * 
     * This test verifies that the ternary operator approach (the fix) works
     * correctly with boolean, integer, and string values, unlike the old
     * match statement which only worked with strings '0' and '1'.
     *
     * @return void
     */
    public function test_ternary_operator_works_with_all_value_types()
    {
        // Simulate the fixed code: $state ? 'success' : 'warning'
        $getColor = fn ($state): string => $state ? 'success' : 'warning';
        $getIcon = fn ($state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';

        // Test with boolean values (PostgreSQL behavior)
        $this->assertEquals('success', $getColor(true), 'Boolean true should work');
        $this->assertEquals('warning', $getColor(false), 'Boolean false should work (CRITICAL FIX)');
        $this->assertEquals('heroicon-o-check-circle', $getIcon(true), 'Boolean true icon');
        $this->assertEquals('heroicon-o-x-circle', $getIcon(false), 'Boolean false icon (CRITICAL FIX)');

        // Test with integer values (MySQL behavior)
        $this->assertEquals('success', $getColor(1), 'Integer 1 should work');
        $this->assertEquals('warning', $getColor(0), 'Integer 0 should work');

        // Test with string values (legacy compatibility)
        $this->assertEquals('success', $getColor('1'), 'String "1" should work');
        $this->assertEquals('warning', $getColor('0'), 'String "0" should work');

        // Test with empty string (what boolean false becomes when cast to string)
        $this->assertEquals('warning', $getColor(''), 'Empty string should work as falsy');
    }
}
