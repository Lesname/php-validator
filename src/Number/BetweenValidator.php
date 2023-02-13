<?php
declare(strict_types=1);

namespace LessValidator\Number;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;

/**
 * @psalm-immutable
 */
final class BetweenValidator implements Validator
{
    public function __construct(public readonly float | int | null $minimal, public readonly float | int | null $maximal)
    {}

    public function validate(mixed $input): ValidateResult
    {
        assert(is_float($input) || is_int($input));

        if ($this->minimal !== null && $input < $this->minimal) {
            if ($this->maximal) {
                return new ErrorValidateResult(
                    'validation.number.between',
                    [
                        'minimal' => $this->minimal,
                        'maximal' => $this->maximal,
                    ],
                );
            }

            return new ErrorValidateResult(
                'validation.number.tooLittle',
                ['minimal' => $this->minimal],
            );
        }

        if ($this->maximal !== null && $input > $this->maximal) {
            if ($this->minimal) {
                return new ErrorValidateResult(
                    'validation.number.between',
                    [
                        'minimal' => $this->minimal,
                        'maximal' => $this->maximal,
                    ],
                );
            }

            return new ErrorValidateResult(
                'validation.number.tooGreat',
                ['maximal' => $this->maximal],
            );
        }

        return new ValidValidateResult();
    }
}
