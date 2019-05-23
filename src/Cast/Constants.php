<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast;

interface Constants
{
    public const NAME                           = 'Cast';
    public const VERSION                        = '1.0.0';
    public const VALID_PODCAST_AUDIO_MIME_TYPES = [
        'audio/x-hx-aac-adts',
        'audio/aac',
        'audio/x-m4a',
        'audio/m4a',
        'audio/ogg',
        'application/x-ogg',
        'application/ogg',
        'audio/x-ogg',
        'audio/mpeg',
        'audio/mpeg3',
        'audio/x-mpeg-3',
        'audio/mp3',
        'audio/mpg',
        'audio/x-mpeg',
        'audio/x-mpeg3',
        'audio/x-mpg',
    ];
    public const MIME_NORMALIZE                 = [
        'audio/x-hx-aac-adts' => 'audio/aac',
        'audio/aac' => 'audio/aac',
        'audio/x-m4a' => 'audio/m4a',
        'audio/m4a' => 'audio/m4a',
        'audio/ogg' => 'audio/ogg',
        'application/x-ogg' => 'audio/ogg',
        'application/ogg' => 'audio/ogg',
        'audio/x-ogg' => 'audio/ogg',
        'audio/mpeg' => 'audio/mpeg',
        'audio/mpeg3' => 'audio/mpeg',
        'audio/x-mpeg-3' => 'audio/mpeg',
        'audio/mp3' => 'audio/mpeg',
        'audio/mpg' => 'audio/mpeg',
        'audio/x-mpeg' => 'audio/mpeg',
        'audio/x-mpeg3' => 'audio/mpeg',
        'audio/x-mpg' => 'audio/mpeg',
    ];
}
