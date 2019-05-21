<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Migration;

use BuzzingPixel\Cast\Cast\Migration\MigrationContract;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use CI_DB_forge;

class M000003AddCastAudioUploadKeysTable implements MigrationContract
{
    /** @var QueryBuilderFactory */
    private $queryBuilderFactory;
    /** @var CI_DB_forge */
    private $dbForge;

    public function __construct(
        QueryBuilderFactory $queryBuilderFactory,
        CI_DB_forge $dbForge
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->dbForge             = $dbForge;
    }

    public function safeUp() : bool
    {
        $tableExists = $this->queryBuilderFactory->make()->table_exists('cast_audio_upload_keys');

        if ($tableExists) {
            return true;
        }

        $this->dbForge->add_field([
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
        ]);

        $this->dbForge->add_key('id', true);

        $this->dbForge->create_table('cast_audio_upload_keys', true);

        return true;
    }

    public function safeDown() : bool
    {
        $tableExists = $this->queryBuilderFactory->make()->table_exists('cast_audio_upload_keys');

        if (! $tableExists) {
            return true;
        }

        $this->dbForge->drop_table('cast_audio_upload_keys');

        return true;
    }
}
