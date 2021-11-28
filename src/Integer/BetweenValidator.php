<?php
declare(strict_types=1);

namespace LessValidator\Integer;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;
use RuntimeException;

/**
 * @psalm-immutable
 */
final class BetweenValidator implements Validator
{
    public function __construct(public float | int $minimal, public float | int $maximal)
    {}

    public function validate(mixed $input): ValidateResult
    {
        assert(is_int($input), new RuntimeException());

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
