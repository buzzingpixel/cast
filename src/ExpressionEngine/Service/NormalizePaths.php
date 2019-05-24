<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Service;

use EE_Config;
use const DIRECTORY_SEPARATOR;
use function rtrim;
use function str_replace;

class NormalizePaths
{
    /** @var EE_Config */
    private $eeConfig;

    public function __construct(
        EE_Config $eeConfig
    ) {
        $this->eeConfig = $eeConfig;
    }

    public function normalize(string $path) : string
    {
        $baseUrl = rtrim($this->eeConfig->item('base_url'), '/');

        $basePath = rtrim($this->eeConfig->item('base_path'), '/');

        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

        $path = rtrim($path, '/');

        $path = rtrim($path, DIRECTORY_SEPARATOR);

        return str_replace(
            [
                '{base_url}',
                '{base_path}',
            ],
            [
                $baseUrl,
                $basePath,
            ],
            $path
        );
    }
}
