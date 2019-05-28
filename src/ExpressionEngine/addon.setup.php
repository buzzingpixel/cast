<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Constants;
use function Safe\spl_autoload_register;

// @codeCoverageIgnoreStart
$autoloader = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    /** @noinspection PhpIncludeInspection */
    require_once $autoloader;
}

/** @noinspection PhpUnhandledExceptionInspection */
spl_autoload_register(static function ($class) : void {
    if ($class === 'CI_DB_forge') {
        ee()->load->dbforge();

        return;
    }
});
// @codeCoverageIgnoreEnd

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
