<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine\language\english;

use BuzzingPixel\Cast\Cast\Language\EnglishTranslations;
use PHPUnit\Framework\TestCase;

class EnglishLangTest extends TestCase
{
    public function testEnglishLang() : void
    {
        $lang = [];

        include TESTING_APP_PATH . '/src/ExpressionEngine/language/english/lang.cast.php';

        self::assertEquals(EnglishTranslations::TRANSLATIONS, $lang);
    }
}
