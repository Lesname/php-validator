<?php
declare(strict_types=1);

namespace LessValidator\Number;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;

/**
 * @psalm-immutable
 *
 * @deprecated use MultipleOfValidator
 */
final class PrecisionValidator implements Validator
{
    public function __construct(private readonly int $precision)
    {
        assert($precision >= 1 && $precision <= 5, "Precision must be between 1 and 5, given {$precision}");
    }

    public function validate(mixed $input): ValidateResult
    {
        assert(is_float($input) || is_int($input));

        if (is_float($input) && preg_match('/\.(\d*)$/', (string)$input, $matches)) {
            if (strlen($matches[1]) > $this->precision) {
                return new ErrorValidateResult(
                    'number.precision',
                    ['max' => $this->precision],
                );
            }
        }

        return new ValidValidateResult();
    }
}
