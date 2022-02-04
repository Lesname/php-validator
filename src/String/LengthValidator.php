<?php
declare(strict_types=1);

namespace LessValidator\String;

use LessValidator\Exception\UnexpectedType;
use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;

/**
 * @psalm-immutable
 */
final class LengthValidator implements Validator
{
    public function __construct(
        public readonly int $minLength,
        public readonly int $maxLength,
    ) {}

    public function validate(mixed $input): ValidateResult
    {
        assert(is_string($input), new UnexpectedType('string', get_debug_type($input)));

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
