<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MigrateMediaToDefaultPaths extends Command
{
    protected $signature = 'media:migrate-to-default-paths
                            {--disk= : The disk to migrate (defaults to media-library disk)}
                            {--dry-run : Show what would be moved without actually moving files}
                            {--chunk=50 : Number of media items to process at once}
                            {--force : Force the operation to run without confirmation}';

    protected $description = 'Migrate media files from custom path structure to default path structure';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $diskName = $this->option('disk') ?: config('media-library.disk_name');
        $chunkSize = (int) $this->option('chunk');

        $this->info("📀 Target disk: {$diskName}");

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No files will be moved');
        } else {
            $this->warn('⚠️  This will move files on the production disk!');
            if (! $this->option('force')) {
                if (!$this->confirm('Continue with migration?')) {
                    $this->info('Migration cancelled.');
                    return 0;
                }
            }
        }

        // Use the disk specified in the media records or the one from config
        $defaultPathGenerator = new DefaultPathGenerator();
        $totalMedia = Media::count();

        $this->info("Found {$totalMedia} media records to migrate");

        $moved = 0;
        $skipped = 0;
        $errors = [];

        Media::chunk($chunkSize, function ($mediaChunk) use ($defaultPathGenerator, $dryRun, &$moved, &$skipped, &$errors) {
            foreach ($mediaChunk as $mediaItem) {
                try {
                    // Get the disk for this specific media item
                    $disk = Storage::disk($mediaItem->disk);

                    // Calculate paths using the old custom path generator
                    $oldPathGenerator = new \App\Media\MediaPathResolver();
                    $currentPath = $oldPathGenerator->getPath($mediaItem) . $mediaItem->file_name;
                    $currentConversionPath = $oldPathGenerator->getPathForConversions($mediaItem);

                    // Get new default paths
                    $newPath = $defaultPathGenerator->getPath($mediaItem) . $mediaItem->file_name;
                    $newConversionPath = $defaultPathGenerator->getPathForConversions($mediaItem);

                    // Check if already in correct location
                    if ($currentPath === $newPath) {
                        $skipped++;
                        continue;
                    }

                    // Check if source file exists
                    if (!$disk->exists($currentPath)) {
                        $this->warn("⚠️  Source file not found: {$currentPath}");
                        $skipped++;
                        continue;
                    }

                    if (!$dryRun) {
                        // Move main file
                        $disk->move($currentPath, $newPath);

                        // Move conversions
                        $conversionDir = dirname($currentPath) . '/conversions';
                        if ($disk->exists($conversionDir)) {
                            $conversions = $disk->files($conversionDir);

                            foreach ($conversions as $conversion) {
                                $baseFileName = pathinfo($mediaItem->file_name, PATHINFO_FILENAME);
                                if (str_contains(basename($conversion), $baseFileName)) {
                                    $filename = basename($conversion);
                                    $disk->move($conversion, $newConversionPath . $filename);
                                }
                            }
                        }

                        // Clean up old empty directories (only if safe to do so)
                        if ($mediaItem->disk === 'public' || $mediaItem->disk === 'local') {
                            $this->cleanupEmptyDirectories($disk, dirname($currentPath));
                        }

                        $this->line("✓ Migrated media #{$mediaItem->id}: {$mediaItem->file_name}");
                    } else {
                        $this->line("Would migrate #{$mediaItem->id}: {$currentPath} → {$newPath}");
                    }

                    $moved++;
                } catch (\Exception $e) {
                    $errors[] = "Media ID {$mediaItem->id}: " . $e->getMessage();
                    $this->error("✗ Error with media #{$mediaItem->id}: " . $e->getMessage());
                }
            }
        });

        $this->newLine();

        if ($dryRun) {
            $this->info("✅ Would migrate {$moved} media files");
            $this->info("ℹ️  {$skipped} files already in correct location or not found");
            $this->info('Run without --dry-run to perform the migration');
        } else {
            $this->info("✅ Successfully migrated {$moved} media files");
            $this->info("ℹ️  {$skipped} files skipped (already migrated or not found)");
        }

        if (count($errors) > 0) {
            $this->error('❌ Encountered ' . count($errors) . ' errors:');
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->error('  - ' . $error);
            }
            if (count($errors) > 10) {
                $this->error('  ... and ' . (count($errors) - 10) . ' more errors');
            }
        }

        if (!$dryRun && $moved > 0) {
            $this->newLine();
            $this->warn('⚠️  Remember to update config/media-library.php to use DefaultPathGenerator if not already done!');
        }

        return count($errors) > 0 ? 1 : 0;
    }

    protected function cleanupEmptyDirectories($disk, $path)
    {
        try {
            // Only clean up if directory is empty
            if (empty($disk->allFiles($path)) && empty($disk->directories($path))) {
                $disk->deleteDirectory($path);

                // Recursively clean up parent if empty
                $parent = dirname($path);
                if ($parent !== '.' && $parent !== '' && $parent !== 'products') {
                    $this->cleanupEmptyDirectories($disk, $parent);
                }
            }
        } catch (\Exception $e) {
            // Silently fail on cleanup - not critical
        }
    }
}
