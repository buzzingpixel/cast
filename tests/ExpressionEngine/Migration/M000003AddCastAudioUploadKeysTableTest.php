<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine\Migration;

use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use BuzzingPixel\Cast\ExpressionEngine\Migration\M000003AddCastAudioUploadKeysTable;
use CI_DB_forge;
use EllisLab\ExpressionEngine\Service\Database\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class M000003AddCastAudioUploadKeysTableTest extends TestCase
{
    /** @var MockObject&Query */
    private $query;

    /** @var MockObject&QueryBuilderFactory */
    private $queryBuilderFactory;

    /** @var MockObject&CI_DB_forge */
    private $dbForge;

    /** @var M000003AddCastAudioUploadKeysTable */
    private $migration;

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        $this->query = $this->createMock(Query::class);

        $this->queryBuilderFactory = $this->createMock(QueryBuilderFactory::class);

        $this->queryBuilderFactory->method('make')->willReturn($this->query);

        $this->dbForge = $this->createMock(CI_DB_forge::class);

        $this->migration = new M000003AddCastAudioUploadKeysTable(
            $this->queryBuilderFactory,
            $this->dbForge
        );
    }

    public function testSafeUpTableExists() : void
    {
        $this->query->expects(self::once())
            ->method('table_exists')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(true);

        $this->dbForge->expects(self::never())
            ->method(self::anything());

        self::assertTrue($this->migration->safeUp());
    }

    public function testSafeUpTableDoesNotExist() : void
    {
        $this->query->expects(self::once())
            ->method('table_exists')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(false);

        $this->dbForge->expects(self::at(0))
            ->method('add_field')
            ->with(self::equalTo([
                'id' => [
                    'type' => 'BIGINT',
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'key' => ['type' => 'TEXT'],
                'created' => [
                    'default' => 0,
                    'type' => 'INT',
                    'unsigned' => true,
                ],
                'expires' => [
                    'default' => 0,
                    'type' => 'INT',
                    'unsigned' => true,
                ],
            ]));

        $this->dbForge->expects(self::at(1))
            ->method('add_key')
            ->with(
                self::equalTo('id'),
                self::equalTo(true)
            );

        $this->dbForge->expects(self::at(2))
            ->method('create_table')
            ->with(
                self::equalTo('cast_audio_upload_keys'),
                self::equalTo(true)
            );

        self::assertTrue($this->migration->safeUp());
    }

    public function testSafeDownTableDoesNotExist() : void
    {
        $this->query->expects(self::once())
            ->method('table_exists')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(false);

        $this->dbForge->expects(self::never())
            ->method(self::anything());

        self::assertTrue($this->migration->safeDown());
    }

    public function testSafeDownTableExists() : void
    {
        $this->query->expects(self::once())
            ->method('table_exists')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(true);

        $this->dbForge->expects(self::once())
            ->method('drop_table')
            ->with(self::equalTo('cast_audio_upload_keys'));

        self::assertTrue($this->migration->safeDown());
    }
}
