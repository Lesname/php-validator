<?php

declare(strict_types=1);

namespace LesValidator\ValidateResult;

use Override;

/**
 * @psalm-immutable
 */
final class ValidValidateResult implements ValidateResult
{
    /**
     * @psalm-pure
     */
    #[Override]
    public function isValid(): bool
    {
        return true;
    }

    /**
     * @psalm-pure
     */
    #[Override]
    public function jsonSerialize(): mixed
    {
        return null;
    }
}
