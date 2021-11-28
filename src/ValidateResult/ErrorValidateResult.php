<?php
declare(strict_types=1);

namespace LessValidator\ValidateResult;

/**
 * @psalm-immutable
 */
final class ErrorValidateResult implements ValidateResult
{
    /**
     * @param string $code
     * @param array<mixed> $context
     */
    public function __construct(public string $code, public array $context = [])
    {}

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
            'context' => $this->context,
        ];
    }
}
