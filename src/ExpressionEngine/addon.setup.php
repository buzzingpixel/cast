<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Constants;

return [
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
];
