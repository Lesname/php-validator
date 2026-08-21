<?php

declare(strict_types=1);

namespace LesValidator;

use LesValidator\ValidateResult\ValidateResult;

/**
 * @psalm-mutable
 */
interface Validator
{
    /**
     * @psalm-impure
     */
    public function validate(mixed $input): ValidateResult;
}
