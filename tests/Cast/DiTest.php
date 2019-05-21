<?php

declare(strict_types=1);

namespace Tests\Cast;

use BuzzingPixel\Cast\Cast\Di;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class DiTest extends TestCase
{
    public function test() : void
    {
        $di = Di::diContainer();

        self::assertInstanceOf(ContainerInterface::class, $di);
    }
}
