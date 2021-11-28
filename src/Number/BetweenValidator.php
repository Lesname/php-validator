<?php
declare(strict_types=1);

namespace LessValidator\Number;

use LessValidator\ValidateResult;
use LessValidator\Validator;
use RuntimeException;

/**
 * @psalm-immutable
 */
final class BetweenValidator implements Validator
{
    public function __construct(public float | int $minimal, public float | int $maximal)
    {}

    public function validate(mixed $input): ValidateResult\ValidateResult
    {
        assert(is_float($input) || is_int($input), new RuntimeException());

        if ($input < $this->minimal) {
            return new ValidateResult\ErrorValidateResult(
                'number.between.tooLittle',
                ['minimal' => $this->minimal],
            );
        }

        if ($input > $this->maximal) {
            return new ValidateResult\ErrorValidateResult(
                'number.between.tooGreat',
                ['maximal' => $this->maximal],
            );
        }

        return new ValidateResult\ValidValidateResult();
    }
}
