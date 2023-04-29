<?php
declare(strict_types=1);

namespace LessValidator\ValidateResult;

use stdClass;

/**
 * @psalm-immutable
 */
final class ErrorValidateResult implements ValidateResult
{
    /**
     * @param string $code
     * @param array<string, string | integer | float | array<string | integer | float | null> | null> $context
     */
    public function __construct(
        public readonly string $code,
        public readonly array $context = [],
    ) {}

    public function isValid(): bool
    {
        return false;
    }

    /**
     * @return array<mixed>
     */
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
