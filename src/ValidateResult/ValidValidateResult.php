<?php
declare(strict_types=1);

namespace LesValidator\ValidateResult;

/**
 * @psalm-immutable
 */
final class ValidValidateResult implements ValidateResult
{
    public function isValid(): bool
    {
        return true;
    }

    public function jsonSerialize(): mixed
    {
        return null;
    }
}
