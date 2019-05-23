<?php

declare(strict_types=1);

namespace Tests\Cast\Uploading;

use BuzzingPixel\Cast\Cast\Uploading\FlysystemFactory;
use BuzzingPixel\Cast\Cast\Uploading\FtpConfig;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class FlysystemFactoryTest extends TestCase
{
    /** @var FlysystemFactory */
    private $factory;

    protected function setUp() : void
    {
        $this->factory = new FlysystemFactory();
    }

    /**
     * @throws Throwable
     */
    public function testMakeFtp() : void
    {
        /** @var MockObject&FtpConfig $ftpConfig */
        $ftpConfig = $this->createMock(FtpConfig::class);

        $ftpConfig->expects(self::once())
            ->method('toArray')
            ->willReturn(['host' => 'ftp.testing.com']);

        $instance = $this->factory->makeFtp($ftpConfig);

        self::assertInstanceOf(Filesystem::class, $instance);

        /** @var Ftp $adapter */
        $adapter = $instance->getAdapter();

        self::assertInstanceOf(Ftp::class, $adapter);

        self::assertEquals('ftp.testing.com', $adapter->getHost());
    }
}
