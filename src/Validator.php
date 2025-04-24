<?php
declare(strict_types=1);

namespace LesValidator;

use LesValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
interface Validator
{
    public function validate(mixed $input): ValidateResult;
}
