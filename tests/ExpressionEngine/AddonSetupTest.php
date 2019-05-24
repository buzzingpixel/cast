<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine;

use BuzzingPixel\Cast\Cast\Constants;
use PHPUnit\Framework\TestCase;

class AddonSetupTest extends TestCase
{
    public function testAddonSetup() : void
    {
        $arr = require TESTING_APP_PATH . '/src/ExpressionEngine/addon.setup.php';

        self::assertSame(
            [
                'author' => 'TJ Draper',
                'author_url' => 'https://buzzingpixel.com',
                'description' => 'Podcasting tools for CMSes',
                'name' => Constants::NAME,
                'namespace' => '\\',
                'version' => Constants::VERSION,
                'fieldtypes' => [
                    'cast_audio' => [
                        'name' => 'Cast Audio',
                        'compatibility' => 'cast_audio',
                    ],
                    'cast_episode_number' => [
                        'name' => 'Cast Episode Number',
                        'compatibility' => 'cast_episode_number',
                    ],
                ],
            ],
            $arr
        );
    }
}
