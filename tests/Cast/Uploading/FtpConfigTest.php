<?php

declare(strict_types=1);

namespace Tests\Cast\Uploading;

use BuzzingPixel\Cast\Cast\Uploading\FtpConfig;
use PHPUnit\Framework\TestCase;

class FtpConfigTest extends TestCase
{
    public function test() : void
    {
        $configArray = [
            'host' => 'testHost',
            'username' => 'testUsername',
            'password' => 'testPassword',
            'port' => 123,
            'root' => '/foo/bar',
            'passive' => false,
            'ssl' => true,
            'timeout' => 321,
        ];

        $config = new FtpConfig($configArray);

        self::assertEquals($configArray, $config->toArray());
    }
}
