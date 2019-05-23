<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Service;

use BuzzingPixel\Cast\Cast\Uploading\CacheLocationServiceContract;
use BuzzingPixel\Cast\ExpressionEngine\Constants;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;
use function is_dir;
use function rtrim;
use function Safe\filemtime;
use function Safe\glob;
use function Safe\strtotime;
use function time;

class CacheLocationService implements CacheLocationServiceContract
{
    /** @var Filesystem */
    private $filesystem;
    /** @var UuidFactory */
    private $uuidFactory;

    public function __construct(
        Filesystem $filesystem,
        UuidFactory $uuidFactory
    ) {
        $this->filesystem = $filesystem;

        $this->filesystem->mkdir(Constants::CAST_CACHE_PATH, 0777);

        $this->cleanUpDir(Constants::CAST_CACHE_PATH);
        $this->uuidFactory = $uuidFactory;
    }

    private function cleanUpDir(string $dirPath) : void
    {
        // Normalize dir path
        $dirPath = rtrim($dirPath, DIRECTORY_SEPARATOR);

        // Some environments can't distinguish between empty match and an error
        /** @noinspection PhpUnhandledExceptionInspection */
        $glob = glob($dirPath . '/*') ?: [];

        // Iterate through items in directory
        foreach ($glob as $item) {
            // Check if item is directory
            if (is_dir($item)) {
                // Run clean up
                $this->cleanUpDir($item);

                // Check for items in directory
                /** @noinspection PhpUnhandledExceptionInspection */
                $items = glob($item . '/*');

                // If there are no items in the directory, remove it
                if (! $items) {
                    $this->filesystem->remove($item);
                }
            } elseif ($this->filesystem->exists($item)) { // File
                // Check if file is older than 1 day
                /** @noinspection PhpUnhandledExceptionInspection */
                if (strtotime('+ 1 day', filemtime($item)) < time()) {
                    // Delete the file
                    $this->filesystem->remove($item);
                }
            }
        }
    }

    public function getCacheLocation() : string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $location = Constants::CAST_CACHE_PATH . $this->uuidFactory->uuid4()->toString();

        $this->filesystem->mkdir($location, 0777);

        return $location . DIRECTORY_SEPARATOR;
    }
}
