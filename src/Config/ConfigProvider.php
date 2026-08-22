<?php

declare(strict_types=1);

namespace LesValidator\Config;

/**
 * @psalm-immutable
 */
final class ConfigProvider
{
    /**
     * @return array<string, mixed>
     *
     * @psalm-pure
     */
    public function __invoke(): array
    {
        return [
            'translator' => [
                'translation' => [
                    'nl_NL' => [
                        __DIR__ . '/../../resource/translation/nl_NL.php',
                    ],
                    'en_US' => [
                        __DIR__ . '/../../resource/translation/en_US.php',
                    ],
                ],
            ],
        ];
    }
}
