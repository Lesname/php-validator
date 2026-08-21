<?php

declare(strict_types=1);

namespace LesValidator;

/**
 * @psalm-immutable
 */
final class TranslationHelper
{
    /**
     * @psalm-mutation-free
     */
    private function __construct()
    {}

    /**
     * @psalm-pure
     */
    public static function getTranslationDirectory(): string
    {
        return __DIR__ . '/../resource/translation';
    }
}
