<?php
declare(strict_types=1);

namespace LesValidator\Composite;

use Override;
use LesValidator\Validator;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\Composite\PropertiesValidateResult;

/**
 * @psalm-immutable
 */
final class RangeValidator implements Validator
{
    public function __construct(
        private readonly string $minKey = 'min',
        private readonly string $maxKey = 'max',
    ) {}

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input));

        if (isset($input[$this->minKey], $input[$this->maxKey])) {
            $min = $input[$this->minKey];
            $max = $input[$this->maxKey];

            assert(is_float($min) || is_int($min));
            assert(is_float($max) || is_int($max));

            if ($min > $max) {
                return new PropertiesValidateResult(
                    [
                        $this->minKey => new ErrorValidateResult(
                            'range.minGreater',
                        ),
                    ],
                );
            }
        }

        return new ValidValidateResult();
    }
}
