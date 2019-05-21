<?php

declare(strict_types=1);

namespace Tests\Cast\Facade;

use BuzzingPixel\Cast\Cast\Facade\PhpInternals;
use PHPUnit\Framework\TestCase;

class PhpInternalsTest extends TestCase
{
    public function testIsDir() : void
    {
        $phpInternals = new PhpInternals();

        self::assertTrue($phpInternals->isDir(TESTS_BASE_PATH . '/filesystemTesting'));

        self::assertFalse($phpInternals->isDir('foo'));
    }
}
