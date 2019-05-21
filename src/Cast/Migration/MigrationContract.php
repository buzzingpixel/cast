<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Migration;

interface MigrationContract
{
    public function safeUp() : bool;

    public function safeDown() : bool;
}
