<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Migration;

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Migration\MigrationContract;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;

class MFinal implements MigrationContract
{
    /** @var QueryBuilderFactory */
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public function safeUp() : bool
    {
        $this->queryBuilderFactory->make()->update(
            'modules',
            ['module_version' => Constants::VERSION],
            ['module_name' => 'Cast']
        );

        return true;
    }

    public function safeDown() : bool
    {
        return true;
    }
}
