<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine\Service;

use BuzzingPixel\Cast\ExpressionEngine\Service\NormalizePaths;
use EE_Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class NormalizePathsTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testNormalize() : void
    {
        /** @var MockObject&EE_Config $eeConfig */
        $eeConfig = $this->createMock(EE_Config::class);

        $eeConfig->method('item')
            ->willReturnCallback(static function ($item) {
                switch ($item) {
                    case 'base_url':
                        return 'https://foobar.com/';

                        break;
                    case 'base_path':
                        return '/base/path/foo/bar/';

                        break;
                    default:
                        return null;
                }
            });

        $normalizePaths = new NormalizePaths($eeConfig);

        self::assertSame(
            'https://foobar.com/baz/base/path/foo/bar/bap',
            $normalizePaths->normalize('{base_url}/baz{base_path}/bap')
        );
    }
}
