<?php
declare(strict_types=1);

namespace LesValidator\String;

use Override;
use RuntimeException;
use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\Validator;

final class LengthValidator implements Validator
{
    public function __construct(
        public readonly ?int $minLength,
        public readonly ?int $maxLength,
    ) {}

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_string($input));

        $length = grapheme_strlen($input);

        if ($length === false) {
            throw new RuntimeException();
        }

        $context = [
            'givenLength' => $length,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
        ];

        if ($this->minLength !== null && $length < $this->minLength) {
            if ($length === 0) {
                return new ErrorValidateResult('string.required');
            }

            return new ErrorValidateResult('string.tooShort', $context);
        }

        if ($this->maxLength !== null && $length > $this->maxLength) {
            return new ErrorValidateResult('string.tooLong', $context);
        }

        return new ValidValidateResult();
    }
}
