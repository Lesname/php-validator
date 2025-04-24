<?php
declare(strict_types=1);

namespace LesValidator;

final class TranslationHelper
{
    private function __construct()
    {}

    public static function getTranslationDirectory(): string
    {
        return __DIR__ . '/../resource/translation';
    }
}
