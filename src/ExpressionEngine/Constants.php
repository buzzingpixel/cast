<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine;

use const DIRECTORY_SEPARATOR;

interface Constants
{
    public const CAST_CACHE_PATH = SYSPATH . 'user' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'cast' . DIRECTORY_SEPARATOR;
}
