<?php
declare(strict_types=1);

namespace LessValidator\Number;

use LessValidator\Validator;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;

/**
 * @psalm-immutable
 */
final class MultipleOfValidator implements Validator
{
    public function __construct(private readonly float|int $multipleOf)
    {
        assert($multipleOf > 0, "Multiple of must be >0, gotten '{$multipleOf}'");
    }

    public function validate(mixed $input): ValidateResult
    {
        assert(is_float($input) || is_int($input));

        if (!self::isMultipleOf($input, $this->multipleOf)) {
            return new ErrorValidateResult(
                'number.notMultipleOf',
                ['multipleOf' => $this->multipleOf],
            );
        }

        return new ValidValidateResult();
    }

    /**
     * @psalm-pure
     */
    private static function isMultipleOf(float | int $value, float | int $of): bool
    {
        if (is_int($value) && is_int($of) && $value % $of === 0) {
            return true;
        }

        if (preg_match('/^(\d+)\.(\d+)$/', (string)$of, $matches)) {
            $precision = strlen($matches[2]);
            $of = (int)($matches[1] . $matches[2]);
            $power = pow(10, $precision);
        } else {
            $precision = 0;
            $power = 1;
        }

        if (preg_match('/^(\d+)\.(\d+)$/', (string)$value, $matches)) {
            if (strlen($matches[2]) > $precision) {
                return false;
            } else {
                $float = str_pad($matches[2], $precision, '0');
                $check = (int)($matches[1] . $float);
            }
        } else {
            $check = $value * $power;
        }

        if ($check % $of !== 0) {
            return false;
        }

        return true;
    }
}
