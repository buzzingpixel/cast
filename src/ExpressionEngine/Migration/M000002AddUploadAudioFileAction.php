<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Migration;

use BuzzingPixel\Cast\Cast\Migration\MigrationContract;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;

class M000002AddUploadAudioFileAction implements MigrationContract
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
            ->where('class', 'Cast')
            ->count_all_results('actions');

        if ($query > 0) {
            return true;
        }

        $this->queryBuilderFactory->make()->insert('actions', [
            'class' => 'Cast',
            'method' => 'uploadAudioFile',
            'csrf_exempt' => '0',
        ]);

        return true;
    }

    public function safeDown() : bool
    {
        $this->queryBuilderFactory->make()->delete('actions', [
            'class' => 'Cast',
            'method' => 'uploadAudioFile',
        ]);

        return true;
    }
}
