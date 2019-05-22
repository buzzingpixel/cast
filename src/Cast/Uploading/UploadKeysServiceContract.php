<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Uploading;

/**
 * Implementing class should clean up expired keys in the constructor
 */
interface UploadKeysServiceContract
{
    /**
     * Creates an upload key, saves it to the database, and returns is
     */
    public function createKey() : string;

    /**
     * Returns false if key is invalid, returns true if key is valid
     */
    public function validateKey(string $key) : bool;
}
