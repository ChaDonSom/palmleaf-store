# Lesson: Code Review Feedback and Schema Verification

**Date:** November 15, 2025  
**Context:** Release/3 PR code review feedback addressing  
**Related Files:** `app/Filament/Widgets/OrdersAwaitingCaptureWidget.php`, `app/PaymentTypes/PaypalPayment.php`

## Problem

During code review feedback implementation, two critical errors were introduced:

1. **Database column error**: Attempted to select `currency_id` column that doesn't exist
2. **Method signature incompatibility**: Changed parent method signature breaking PHP inheritance rules

Both errors only surfaced at runtime after deployment, not during testing.

## Root Causes

### 1. Schema Assumptions Without Verification

When optimizing database queries, assumed the Order model had a `currency_id` foreign key column:

```php
// ❌ WRONG - Assumed schema without checking
$firstOrder = (clone $baseQuery)
    ->select(['id', 'currency_id'])  // currency_id doesn't exist!
    ->with('currency')
    ->first();
```

**Reality**: The `lunar_orders` table uses `currency_code` (string) not `currency_id` (foreign key). The `currency` relationship is defined differently in Lunar.

### 2. Incomplete Parent Class Signature Research

When addressing backward compatibility feedback, changed method signature without fully checking parent requirements:

```php
// ❌ WRONG - Added default value that parent doesn't have
public function refund(Transaction $transaction, int $amount = 0, $notes = null)

// Parent actually requires:
public function refund(Contracts\Transaction $transaction, int $amount, $notes = null)
```

This violates [Liskov Substitution Principle](https://en.wikipedia.org/wiki/Liskov_substitution_principle) - child methods must be compatible with parent.

## Solutions

### Always Verify Database Schema

Before optimizing queries or selecting specific columns:

```bash
# Check migration files
grep -r "currency" database/migrations/*create_orders*.php

# Or inspect the actual database schema
php artisan db:show lunar_orders

# Or use tinker to inspect the model
php artisan tinker
>>> \Lunar\Models\Order::first()->getAttributes()
```

**Correct approach**: Let Eloquent load what's needed via relationships:

```php
// ✅ CORRECT - Trust the relationship, don't assume columns
$firstOrder = (clone $baseQuery)->with('currency')->first();
// Access: $firstOrder->currency->code
```

### Always Verify Parent Method Signatures

Before overriding methods, check the exact parent signature:

```bash
# Using reflection
php -r "require 'vendor/autoload.php'; 
\$r = new ReflectionMethod('Lunar\PaymentTypes\AbstractPayment', 'refund'); 
foreach(\$r->getParameters() as \$p) { 
    echo \$p->getName() . ': ' . \$p->getType() . ' default=' . 
        (\$p->isDefaultValueAvailable() ? var_export(\$p->getDefaultValue(), true) : 'none') . PHP_EOL; 
}"

# Or check the source directly
grep -A5 "public function refund" vendor/lunarphp/*/src/PaymentTypes/AbstractPayment.php
```

## When This Applies

### Critical Times to Verify Schema

-   **Optimizing queries** with `select()` - especially for third-party package models
-   **Adding eager loading** - verify the relationship actually exists and how it works
-   **Writing raw SQL** - always check actual column names
-   **Joining tables** - verify foreign key columns and their names
-   **Migration updates** - check existing schema before altering

### Critical Times to Verify Method Signatures

-   **Implementing abstract methods** - must match exactly
-   **Overriding parent methods** - must be compatible (same or more permissive)
-   **Addressing "backward compatibility" concerns** - verify what the actual contract requires
-   **Using type hints** - use correct namespace (e.g., `Contracts\Transaction` vs `Transaction`)

## Prevention Checklist

Before committing database-related changes:

- [ ] Verified actual column names in migration files or database
- [ ] Tested queries against actual database schema
- [ ] Checked if optimization is actually needed (avoid premature optimization)
- [ ] Used relationships instead of manual column selection when possible

Before overriding methods:

- [ ] Checked parent class signature with reflection or source code
- [ ] Verified parameter types match (including namespaces)
- [ ] Verified default values match parent's defaults
- [ ] Ran type checker or static analysis if available
- [ ] Confirmed all tests pass

## Key Takeaways

1. **Never assume database schema** - Always verify column names, especially for third-party packages like Lunar
2. **Trust Eloquent relationships** - Don't prematurely optimize with `select()` unless proven bottleneck
3. **Method signatures are contracts** - Child classes must honor parent signatures exactly
4. **Automated tests don't catch everything** - Schema errors and type compatibility only surface at runtime
5. **Code review feedback can introduce bugs** - Apply feedback carefully with same rigor as original code

## Testing Gaps

This incident revealed that our test suite doesn't:

-   Run against a real database schema (tests use in-memory SQLite which is more permissive)
-   Verify method signature compatibility at test time
-   Test widget queries with actual data

**Future improvement**: Consider adding integration tests that run against actual MySQL schema to catch column name errors.

## Technical Details

### Why Tests Didn't Catch These Errors

1. **SQLite schema differences**: Test database might have different column names or be more permissive
2. **No static analysis**: PHP doesn't check method signature compatibility until runtime
3. **Widget not tested**: `OrdersAwaitingCaptureWidget` had no direct tests
4. **Mock-heavy tests**: Payment tests mock heavily, never actually calling `refund()`

### PHP Method Signature Compatibility Rules

Child method signatures must be **contravariant** in parameters and **covariant** in return types:

-   Can accept **more general** parameter types (but not more specific)
-   Can add **optional** parameters (but cannot remove required ones)
-   Can remove default values (making parameters required)
-   Cannot add default values to required parameters

## Related Resources

-   [Laravel Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)
-   [Lunar Schema Documentation](https://docs.lunarphp.io/)
-   [PSP-12 Method Signatures](https://www.php-fig.org/psr/psr-12/)
-   [Liskov Substitution Principle](https://en.wikipedia.org/wiki/Liskov_substitution_principle)
