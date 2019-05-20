<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Templating;

use const DIRECTORY_SEPARATOR;
use function dirname;
use function explode;
use function extract;
use function implode;
use function ob_get_clean;
use function ob_get_status;
use function ob_start;

// phpcs:disable Generic.PHP.ForbiddenFunctions.Found

class TemplatingService
{
    /** @var string */
    private $baseTemplateDir;

    public function __construct()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->baseTemplateDir = dirname(__DIR__, 2) . $sep . 'Templates' . $sep;
    }

    /**
     * @param mixed[] $vars
     */
    public function render(string $path, array $vars = []) : string
    {
        $path = explode('/', $path);
        $path = implode(DIRECTORY_SEPARATOR, $path);

        $bufferUsed = ob_get_status()['buffer_used'] ?? 0;

        $previousBuffer = '';

        if ($bufferUsed) {
            $previousBuffer = ob_get_clean();
        }

        ob_start();

        extract($vars);

        /** @noinspection PhpIncludeInspection */
        include $this->baseTemplateDir . $path . '.php';

        $output = ob_get_clean();

        if ($bufferUsed) {
            ob_start();

            echo $previousBuffer;
        }

        return (string) $output;
    }
}
