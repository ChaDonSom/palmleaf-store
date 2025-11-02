# Image Loading Fix - Documentation

## Problem
Product images were inconsistently failing to load with 404 errors (approximately 20% failure rate). Both the storefront and hub were affected.

## Root Cause Analysis

The issue was caused by a race condition in the media conversion pipeline:

1. **Queued Conversions**: The `config/media-library.php` had `queue_conversions_by_default` set to `true`, meaning image conversions (like 'large' and 'small' sizes) were generated asynchronously via queue jobs.

2. **Race Condition**: When a product image was uploaded:
   - The original image was saved immediately
   - The page would reference conversion URLs (e.g., `/products/2025/10/27/conversions/filename--large.png`)
   - But the conversion job might not have run yet
   - Result: 404 errors for images that didn't exist yet

3. **CDN Caching**: Cloudflare CDN would cache these 404 responses, meaning even after the conversion was generated, users would continue to see 404s until the cache expired.

## Solution

### 1. SafeUrlGenerator (app/Media/SafeUrlGenerator.php)

A custom URL generator that checks if a conversion has been generated before returning its URL:

```php
- Checks the `generated_conversions` field in the media table
- If a conversion hasn't been generated, falls back to the original image URL
- Prevents 404 errors by always serving a valid image
```

**Benefits:**
- Fixes existing images with missing conversions
- Provides graceful degradation
- Users always see an image (even if not the optimized conversion)

### 2. Disabled Queued Conversions by Default

Changed `queue_conversions_by_default` from `true` to `false` in `config/media-library.php`:

```php
'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', false),
```

**Benefits:**
- Conversions are generated synchronously during upload
- Eliminates the race condition
- Images are available immediately when the page loads

### 3. Environment Variable

Added `QUEUE_CONVERSIONS_BY_DEFAULT=false` to `.env.example` for easy configuration.

## Configuration

### To Use Synchronous Conversions (Recommended)
Set in your `.env` file:
```bash
QUEUE_CONVERSIONS_BY_DEFAULT=false
```

This ensures all conversions are generated immediately during upload. This is the safest option but may increase upload times slightly for large images.

### To Use Queued Conversions (Advanced)
If you prefer queued conversions for performance reasons:

1. Set in your `.env` file:
```bash
QUEUE_CONVERSIONS_BY_DEFAULT=true
QUEUE_CONNECTION=redis  # or another persistent queue driver
```

2. Ensure your queue worker is running:
```bash
php artisan queue:work
```

3. The SafeUrlGenerator will still provide fallback to original images until conversions complete.

## Testing the Fix

### For Existing Images with 404s

1. The SafeUrlGenerator will automatically fall back to original images
2. Cloudflare cache will need to clear naturally (based on cache headers) or be manually purged
3. You can manually regenerate conversions with:
```bash
php artisan media-library:regenerate
```

### For New Images

1. Upload a new product image
2. The conversion will be generated immediately (if using synchronous mode)
3. The image should display correctly on first load

## Technical Details

### How SafeUrlGenerator Works

When `getUrl('large')` is called on a media object:

1. Check if this is a conversion request (not the original file)
2. Look at the `generated_conversions` JSON field in the media table
3. If the conversion exists and is marked as generated, return the conversion URL
4. If not, temporarily clear the conversion name and return the original image URL
5. Restore the conversion name to avoid side effects

### Media Path Structure

The `MediaPathResolver` class generates paths based on the created_at timestamp:
```
/products/YYYY/MM/DD/filename.ext                    # Original
/products/YYYY/MM/DD/conversions/filename--large.png  # Conversion
```

### Conversions Defined in Lunar

Lunar's `StandardMediaConversions` typically defines:
- `small` - Thumbnail size
- `large` - Display size

These are referenced in the product page Blade template.

## Monitoring

To monitor conversion generation:

```sql
-- Check if conversions are generated
SELECT id, file_name, generated_conversions 
FROM media 
WHERE model_type = 'Lunar\\Models\\Product'
LIMIT 10;
```

The `generated_conversions` column should show:
```json
{"small": true, "large": true}
```

## Rollback

If issues occur, you can rollback by:

1. Remove SafeUrlGenerator configuration:
```php
// In config/media-library.php
'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,
```

2. Re-enable queued conversions:
```php
'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),
```

3. Clear config cache:
```bash
php artisan config:clear
```

## Additional Notes

- The fix is backward compatible with existing media
- No database migrations required
- No changes to existing URLs or file structure
- Works with both local and S3/cloud storage
