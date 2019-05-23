<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Uploading;

/**
 * Implementing class should clean up the cache in its constructor
 */
interface CacheLocationServiceContract
{
    /**
     * Returns a location for cache items to be stored
     */
    public function getCacheLocation() : string;
}
