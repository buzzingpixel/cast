<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Migration;

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Migration\MigrationContract;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;

class M000001AddModule implements MigrationContract
{
    /** @var QueryBuilderFactory */
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public function safeUp() : bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('module_name', 'Cast')
            ->count_all_results('modules');

        if ($query > 0) {
            return true;
        }

        $this->queryBuilderFactory->make()->insert('modules', [
            'module_name' => 'Cast',
            'module_version' => Constants::VERSION,
            'has_cp_backend' => 'n',
            'has_publish_fields' => 'n',
        ]);

        return true;
    }

    public function safeDown() : bool
    {
        $this->queryBuilderFactory->make()->delete('modules', ['module_name' => 'Cast']);

        return true;
    }
}
