<?php
declare(strict_types=1);

namespace LesValidator\Collection;

use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\Validator;
use LesValidator\ValidateResult\Collection\SelfValidateResult;

/**
 * @psalm-immutable
 */
final class SizeValidator implements Validator
{
    public function __construct(public readonly ?int $minSize, public readonly ?int $maxSize)
    {}

    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input));

        $size = count($input);

        if ($this->minSize !== null && $size < $this->minSize) {
            return new SelfValidateResult(
                new ErrorValidateResult('collection.tooFew', ['counted' => $size, 'min' => $this->minSize])
            );
        }

        if ($this->maxSize !== null && $size > $this->maxSize) {
            return new SelfValidateResult(
                new ErrorValidateResult('collection.tooMany', ['counted' => $size, 'max' => $this->maxSize]),
            );
        }

        return new ValidValidateResult();
    }
}
