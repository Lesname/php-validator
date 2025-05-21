<?php
declare(strict_types=1);

namespace LesValidator\ValidateResult;

use Override;

/**
 * @psalm-immutable
 */
final class ValidValidateResult implements ValidateResult
{
    #[Override]
    public function isValid(): bool
    {
        return true;
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return null;
    }
}
