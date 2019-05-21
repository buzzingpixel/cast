<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Language;

class Translator
{
    /** @var EnglishTranslations */
    private $translations;

    /**
     * @param mixed|EnglishTranslations $translations
     */
    public function __construct($translations)
    {
        $this->translations = $translations;
    }

    public function getTranslation(string $key) : string
    {
        return $this->translations::TRANSLATIONS[$key] ?? '';
    }
}
