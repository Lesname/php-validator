<?php
declare(strict_types=1);

namespace LesValidator;

use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;

/**
 * @psalm-immutable
 */
final class NullableValidator implements Validator
{
    public function __construct(public readonly Validator $subValidator)
    {}

    public function validate(mixed $input): ValidateResult
    {
        if ($input === null) {
            return new ValidValidateResult();
        }

        return $this->subValidator->validate($input);
    }
}
