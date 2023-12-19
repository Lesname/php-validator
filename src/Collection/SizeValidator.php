<?php
declare(strict_types=1);

namespace LessValidator\Collection;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;
use LessValidator\ValidateResult\Collection\SelfValidateResult;

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
