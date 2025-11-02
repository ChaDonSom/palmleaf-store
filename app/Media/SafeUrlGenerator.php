<?php

namespace App\Media;

use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class SafeUrlGenerator extends DefaultUrlGenerator
{
    /**
     * Get the url to the media file.
     *
     * If requesting a conversion that hasn't been generated yet, this method
     * will fall back to the original media URL instead of returning a URL
     * to a non-existent file. This prevents 404 errors and provides graceful
     * degradation when conversions are missing or still being processed.
     *
     * @return string
     */
    public function getUrl(): string
    {
        // If this is a conversion request, check if the conversion exists
        if ($this->conversion && !$this->conversionFileExists($this->conversion)) {
            // Conversion doesn't exist or hasn't been generated
            // Get the original media URL as fallback
            return $this->getOriginalUrl();
        }

        return parent::getUrl();
    }

    /**
     * Check if a conversion file actually exists on disk/storage.
     *
     * @param Conversion $conversion
     * @return bool
     */
    private function conversionFileExists(Conversion $conversion): bool
    {
        $conversionName = $conversion->getName();
        $generatedConversions = $this->media->generated_conversions;
        
        // First check the database field
        $markedAsGenerated = isset($generatedConversions[$conversionName]) && 
                            $generatedConversions[$conversionName] === true;
        
        if (!$markedAsGenerated) {
            return false;
        }
        
        // Also check if the file actually exists on disk/storage
        try {
            $storage = $this->getDisk();
            $path = $this->getPathRelativeToRoot();
            
            return $storage->exists($path);
        } catch (\Exception $e) {
            // If we can't check, assume it doesn't exist to be safe
            return false;
        }
    }

    /**
     * Get the URL to the original media file (not a conversion).
     *
     * @return string
     */
    private function getOriginalUrl(): string
    {
        // Temporarily clear conversion to get original URL
        $originalConversion = $this->conversion;
        $this->conversion = null;
        $url = parent::getUrl();
        $this->conversion = $originalConversion;
        
        return $url;
    }
}
