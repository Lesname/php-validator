<?php
declare(strict_types=1);

namespace LessValidator\String;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;
use RuntimeException;

/**
 * @psalm-immutable
 */
final class LengthValidator implements Validator
{
    public function __construct(
        public int $minLength,
        public int $maxLength,
    ) {}

    public function validate(mixed $input): ValidateResult
    {
        assert(is_string($input), new RuntimeException());

        $length = mb_strlen($input);
        $context = [
            'givenLength' => $length,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
        ];

        if ($length < $this->minLength) {
            return new ErrorValidateResult('string.length.tooShort', $context);
        }

        if ($length > $this->maxLength) {
            return new ErrorValidateResult('string.length.tooLong', $context);
        }

        return new ValidValidateResult();
    }
}
