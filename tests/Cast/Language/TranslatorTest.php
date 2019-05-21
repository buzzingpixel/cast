<?php

declare(strict_types=1);

namespace Tests\Cast\Language;

use BuzzingPixel\Cast\Cast\Language\EnglishTranslations;
use BuzzingPixel\Cast\Cast\Language\Translator;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    public function test() : void
    {
        $translator = new Translator(new EnglishTranslations());

        self::assertSame('', $translator->getTranslation('foo'));

        self::assertSame(
            EnglishTranslations::TRANSLATIONS['upload_path'],
            $translator->getTranslation('upload_path')
        );
    }
}
