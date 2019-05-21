<?php

declare(strict_types=1);

namespace Tests\Cast\ExpressionEngine\Migration;

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use BuzzingPixel\Cast\ExpressionEngine\Migration\MFinal;
use EllisLab\ExpressionEngine\Service\Database\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class MFinalTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testSafeUp() : void
    {
        $queryBuilder = $this->createMock(Query::class);

        $queryBuilder->expects(self::once())
            ->method('update')
            ->with(
                self::equalTo('modules'),
                self::equalTo(['module_version' => Constants::VERSION]),
                self::equalTo(['module_name' => 'Cast'])
            );

        /** @var MockObject&QueryBuilderFactory $queryBuilderFactory */
        $queryBuilderFactory = $this->createMock(QueryBuilderFactory::class);

        $queryBuilderFactory->method('make')->willReturn($queryBuilder);

        $migration = new MFinal($queryBuilderFactory);

        self::assertTrue($migration->safeUp());

        self::assertTrue($migration->safeDown());
    }
}
