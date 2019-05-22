<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Clock;

use DateTimeImmutable;

final class CommonTime
{
    /** @var DateTimeImmutable|null */
    private static $commonTime;

    public static function getCommonTime() : DateTimeImmutable
    {
        if (! self::$commonTime) {
            self::$commonTime = new DateTimeImmutable();
        }

        return self::$commonTime;
    }
}
