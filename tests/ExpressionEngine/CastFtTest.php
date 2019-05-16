<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine;

use BuzzingPixel\Cast\Cast\Constants;
use Cast_ft;
use PHPUnit\Framework\TestCase;

class CastFtTest extends TestCase
{
    /** @var Cast_ft */
    private $ft;

    public function setUp() : void
    {
        $this->ft = new Cast_ft();
    }

    public function testInfoProperty() : void
    {
        self::assertSame(
            [
                'name' => Constants::NAME,
                'version' => Constants::VERSION,
            ],
            $this->ft->info
        );
    }

    public function testDisplayField() : void
    {
        self::assertSame(
            'TODO: Implement display_field() method.',
            $this->ft->display_field('')
        );
    }
}
