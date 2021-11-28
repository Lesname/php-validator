<?php
declare(strict_types=1);

namespace LessValidator;

use LessValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
interface Validator
{
    public function validate(mixed $input): ValidateResult;
}
