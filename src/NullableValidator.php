<?php
declare(strict_types=1);

namespace LessValidator;

use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;

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
