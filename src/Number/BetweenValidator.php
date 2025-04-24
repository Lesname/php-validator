<?php
declare(strict_types=1);

namespace LesValidator\Number;

use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\Validator;

/**
 * @psalm-immutable
 */
final class BetweenValidator implements Validator
{
    public function __construct(
        public readonly float | int | null $minimal,
        public readonly float | int | null $maximal,
    ) {}

    public function validate(mixed $input): ValidateResult
    {
        assert(is_float($input) || is_int($input));

        if ($this->minimal !== null && $input < $this->minimal) {
            if ($this->maximal !== null) {
                return new ErrorValidateResult(
                    'number.between',
                    [
                        'minimal' => $this->minimal,
                        'maximal' => $this->maximal,
                    ],
                );
            }

            return new ErrorValidateResult(
                'number.tooLittle',
                ['minimal' => $this->minimal],
            );
        }

        if ($this->maximal !== null && $input > $this->maximal) {
            if ($this->minimal !== null) {
                return new ErrorValidateResult(
                    'number.between',
                    [
                        'minimal' => $this->minimal,
                        'maximal' => $this->maximal,
                    ],
                );
            }

            return new ErrorValidateResult(
                'number.tooGreat',
                ['maximal' => $this->maximal],
            );
        }

        return new ValidValidateResult();
    }
}
