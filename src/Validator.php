<?php
declare(strict_types=1);

namespace LesValidator;

use LesValidator\ValidateResult\ValidateResult;

interface Validator
{
    public function validate(mixed $input): ValidateResult;
}
