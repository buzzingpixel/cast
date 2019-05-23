<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine\Service;

use BuzzingPixel\Cast\ExpressionEngine\Constants;
use BuzzingPixel\Cast\ExpressionEngine\Service\CacheLocationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
use const DIRECTORY_SEPARATOR;
use function file_exists;
use function is_dir;
use function mkdir;
use function time;
use function touch;

class CacheLocationServiceTest extends TestCase
{
    /** @var UuidInterface */
    private $uuid;
    /** @var CacheLocationService */
    private $cacheLocationService;

    /**
     * @throws Throwable
     */
    public function setUp() : void
    {
        $this->uuid = (new UuidFactory())->uuid4();

        /** @var MockObject&UuidFactory $uuidFactory */
        $uuidFactory = $this->createMock(UuidFactory::class);

        $uuidFactory->method('uuid4')
            ->willReturn($this->uuid);

        $testDir1 = Constants::CAST_CACHE_PATH . 'test1';

        $testDir2 = Constants::CAST_CACHE_PATH . 'test2';

        $testFile = Constants::CAST_CACHE_PATH . 'test2/file.txt';

        @mkdir($testDir1, 0777, true);

        @mkdir($testDir2, 0777, true);

        touch($testFile, time() - 999999999);

        self::assertTrue(is_dir($testDir1));

        self::assertTrue(is_dir($testDir2));

        self::assertTrue(file_exists($testFile));

        $this->cacheLocationService = new CacheLocationService(
            new Filesystem(),
            $uuidFactory
        );

        self::assertFalse(is_dir($testDir1));

        self::assertFalse(is_dir($testDir2));

        self::assertFalse(file_exists($testFile));
    }

    public function test() : void
    {
        $dir = $this->cacheLocationService->getCacheLocation();

        self::assertTrue(is_dir($dir));

        self::assertSame(
            Constants::CAST_CACHE_PATH . $this->uuid->toString() . DIRECTORY_SEPARATOR,
            $dir
        );
    }
}
