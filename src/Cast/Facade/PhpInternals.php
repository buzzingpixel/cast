<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Facade;

use function is_dir;
use function Safe\filesize;
use function Safe\fopen;
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

    public function fileSize(string $fileName) : int
    {
        // @codeCoverageIgnoreStart
        /** @noinspection PhpUnhandledExceptionInspection */
        return filesize($fileName);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return resource
     */
    public function fopen(string $filename, string $mode)
    {
        // @codeCoverageIgnoreStart
        /** @noinspection PhpUnhandledExceptionInspection */
        return fopen($filename, $mode);
        // @codeCoverageIgnoreEnd
    }

    public function phpExit() : void
    {
        // @codeCoverageIgnoreStart
        exit;
        // @codeCoverageIgnoreEnd
    }
}
