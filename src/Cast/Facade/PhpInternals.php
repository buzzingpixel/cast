<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Facade;

use function is_dir;
use function Safe\mime_content_type;

// phpcs:disable SlevomatCodingStandard.ControlStructures.ControlStructureSpacing.IncorrectLinesCountAfterControlStructure
// phpcs:disable SlevomatCodingStandard.ControlStructures.ControlStructureSpacing.IncorrectLinesCountBeforeFirstControlStructure

class PhpInternals
{
    public function isDir(string $fileName) : bool
    {
        return is_dir($fileName);
    }

    public function mimeContentType(string $fileName) : string
    {
        // @codeCoverageIgnoreStart
        /** @noinspection PhpUnhandledExceptionInspection */
        return mime_content_type($fileName);
        // @codeCoverageIgnoreEnd
    }

    public function phpExit() : void
    {
        // @codeCoverageIgnoreStart
        exit;
        // @codeCoverageIgnoreEnd
    }
}
