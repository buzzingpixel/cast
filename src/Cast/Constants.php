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
}
