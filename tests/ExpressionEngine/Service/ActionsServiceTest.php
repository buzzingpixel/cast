<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine\Service;

use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use BuzzingPixel\Cast\ExpressionEngine\Service\ActionsService;
use CI_DB_result;
use EE_Config;
use EllisLab\ExpressionEngine\Service\Database\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class ActionsServiceTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testGetUploadActionUrl() : void
    {
        $actionRecord = new stdClass();

        $actionRecord->action_id = '1423';

        $dbResult = $this->createMock(CI_DB_result::class);

        $dbResult->expects(self::once())
            ->method('row')
            ->willReturn($actionRecord);

        $queryBuilder = $this->createMock(Query::class);

        $queryBuilder->expects(self::at(0))
            ->method('select')
            ->with(self::equalTo('action_id'))
            ->willReturn($queryBuilder);

        $queryBuilder->expects(self::at(1))
            ->method('where')
            ->with(self::equalTo('class'), self::equalTo('Cast'))
            ->willReturn($queryBuilder);

        $queryBuilder->expects(self::at(2))
            ->method('where')
            ->with(self::equalTo('method'), self::equalTo('uploadAudioFile'))
            ->willReturn($queryBuilder);

        $queryBuilder->expects(self::at(3))
            ->method('get')
            ->with(self::equalTo('actions'))
            ->willReturn($dbResult);

        /** @var MockObject&QueryBuilderFactory $queryBuilderFactory */
        $queryBuilderFactory = $this->createMock(QueryBuilderFactory::class);

        $queryBuilderFactory->expects(self::once())
            ->method('make')
            ->willReturn($queryBuilder);

        /** @var MockObject&EE_Config $eeConfig */
        $eeConfig = $this->createMock(EE_Config::class);

        $eeConfig->expects(self::at(0))
            ->method('item')
            ->with(self::equalTo('site_url'))
            ->willReturn('https://www.foobar.com/');

        $eeConfig->expects(self::at(1))
            ->method('item')
            ->with(self::equalTo('site_index'))
            ->willReturn('test.php');

        $service = new ActionsService(
            $queryBuilderFactory,
            $eeConfig
        );

        self::assertSame(
            'https://www.foobar.com/test.php?ACT=1423',
            $service->getUploadActionUrl()
        );
    }
}
