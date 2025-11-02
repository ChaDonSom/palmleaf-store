<?php

namespace App\Media;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class SafeUrlGenerator extends DefaultUrlGenerator
{
    /**
     * Get the url to the media file.
     *
     * @return string
     */
    public function getUrl(): string
    {
        // If this is a conversion request, check if the conversion exists
        if ($this->conversion) {
            // Check if the conversion has been generated
            $generatedConversions = $this->media->generated_conversions;
            
            if (!isset($generatedConversions[$this->conversion]) || 
                $generatedConversions[$this->conversion] !== true) {
                // Conversion doesn't exist or hasn't been generated
                // Fall back to the original media URL
                $this->conversion = null;
                return parent::getUrl();
            }
        }

        return parent::getUrl();
    }
}
