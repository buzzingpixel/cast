<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Factory;

use EllisLab\ExpressionEngine\Service\Database\Query;

class QueryBuilderFactory
{
    public function make() : Query
    {
        return ee('db');
    }
}
