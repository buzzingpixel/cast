<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Service;

use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use EE_Config;
use function rtrim;

class ActionsService
{
    /** @var QueryBuilderFactory */
    private $queryBuilderFactory;
    /** @var EE_Config */
    private $eeConfig;

    public function __construct(
        QueryBuilderFactory $queryBuilderFactory,
        EE_Config $eeConfig
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->eeConfig            = $eeConfig;
    }

    public function getUploadActionUrl() : string
    {
        $actionRecord = $this->queryBuilderFactory->make()
            ->select('action_id')
            ->where('class', 'Cast')
            ->where('method', 'uploadAudioFile')
            ->get('actions')
            ->row();

        return rtrim($this->eeConfig->item('site_url'), '/') .
            '/' .
            $this->eeConfig->item('site_index') .
            '?ACT=' .
            $actionRecord->action_id;
    }
}
