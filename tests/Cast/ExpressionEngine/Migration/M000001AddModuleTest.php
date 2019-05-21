<?php

declare(strict_types=1);

namespace Tests\Cast\ExpressionEngine\Migration;

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use BuzzingPixel\Cast\ExpressionEngine\Migration\M000001AddModule;
use EllisLab\ExpressionEngine\Service\Database\Query;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class M000001AddModuleTest extends TestCase
{
    /** @var MockObject&QueryBuilderFactory */
    private $queryBuilderFactory;

    /** @var M000001AddModule */
    private $migration;

    /** @var MockObject&Query */
    private $query1;

    /** @var MockObject&Query */
    private $query2;

    /** @var int */
    private $queryMakeCount = 0;

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        $self = $this;

        $this->query1 = $this->createMock(Query::class);

        $this->query2 = $this->createMock(Query::class);

        $this->queryBuilderFactory = $this->createMock(QueryBuilderFactory::class);

        $this->queryBuilderFactory->method('make')
            ->willReturnCallback(static function () use ($self) {
                $self->queryMakeCount++;

                if ($self->queryMakeCount === 1) {
                    return $self->query1;
                }

                if ($self->queryMakeCount === 2) {
                    return $self->query2;
                }

                throw new LogicException('Need more query objects');
            });

        $this->migration = new M000001AddModule($this->queryBuilderFactory);
    }

    public function testSafeUpRecordExists() : void
    {
        $this->query1->expects(self::once())
            ->method('where')
            ->with(
                self::equalTo('module_name'),
                self::equalTo('Cast')
            )
            ->willReturn($this->query1);

        $this->query1->expects(self::once())
            ->method('count_all_results')
            ->with(self::equalTo('modules'))
            ->willReturn(1);

        $this->query2->expects(self::never())
            ->method('insert');

        self::assertTrue($this->migration->safeUp());
    }

    public function testSafeUpRecordDoesNotExist() : void
    {
        $this->query1->expects(self::once())
            ->method('where')
            ->with(
                self::equalTo('module_name'),
                self::equalTo('Cast')
            )
            ->willReturn($this->query1);

        $this->query1->expects(self::once())
            ->method('count_all_results')
            ->with(self::equalTo('modules'))
            ->willReturn(0);

        $this->query2->expects(self::once())
            ->method('insert')
            ->with(
                self::equalTo('modules'),
                self::equalTo([
                    'module_name' => 'Cast',
                    'module_version' => Constants::VERSION,
                    'has_cp_backend' => 'n',
                    'has_publish_fields' => 'n',
                ])
            );

        self::assertTrue($this->migration->safeUp());
    }

    public function testSafeDown() : void
    {
        $this->query1->expects(self::once())
            ->method('delete')
            ->with(
                self::equalTo('modules'),
                self::equalTo(['module_name' => 'Cast'])
            );

        $this->query2->expects(self::never())
            ->method(self::anything());

        self::assertTrue($this->migration->safeDown());
    }
}
