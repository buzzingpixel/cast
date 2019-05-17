<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Facade;

use function is_dir;

class PhpInternals
{
    public function isDir(string $fileName) : bool
    {
        return is_dir($fileName);
    }
}
