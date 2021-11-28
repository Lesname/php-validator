<?php
declare(strict_types=1);

namespace LessValidator;

/**
 * @psalm-immutable
 */
interface Validator
{
    public function validate(mixed $input): ValidateResult\ValidateResult;
}
