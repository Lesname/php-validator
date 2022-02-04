<?php
declare(strict_types=1);

namespace LessValidator\Number;

use LessValidator\Exception\UnexpectedType;
use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;

/**
 * @psalm-immutable
 */
final class BetweenValidator implements Validator
{
    public function __construct(public readonly float | int $minimal, public readonly float | int $maximal)
    {}

    public function validate(mixed $input): ValidateResult
    {
        assert(is_float($input) || is_int($input), new UnexpectedType('number', get_debug_type($input)));

        if ($input < $this->minimal) {
            return new ErrorValidateResult(
                'number.between.tooLittle',
                ['minimal' => $this->minimal],
            );
        }

        if ($input > $this->maximal) {
            return new ErrorValidateResult(
                'number.between.tooGreat',
                ['maximal' => $this->maximal],
            );
        }

        return new ValidValidateResult();
    }
}
