<?php

declare(strict_types=1);

namespace LesValidator\ValidateResult;

use stdClass;
use Override;

/**
 * @psalm-immutable
 */
final class ErrorValidateResult implements ValidateResult
{
    /**
     * @param array<string, string | integer | float | array<string | integer | float | null> | null> $context
     *
     * @psalm-pure
     */
    public function __construct(
        public readonly string $code,
        public readonly array $context = [],
    ) {}

    /**
     * @psalm-pure
     */
    #[Override]
    public function isValid(): bool
    {
        return false;
    }

    /**
     * @return array<mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'context' => $this->context === []
                ? new stdClass()
                : $this->context,
        ];
    }
}
