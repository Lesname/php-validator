<?php
declare(strict_types=1);

namespace LesValidator\Number;

use Override;
use RuntimeException;
use LesValidator\Validator;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;

final class MultipleOfValidator implements Validator
{
    public function __construct(
        private readonly float|int $multipleOf,
        private readonly float|int $offset = 0,
    ) {
        if ($multipleOf <= 0) {
            throw new RuntimeException("Multiple of must be >0, gotten '{$multipleOf}'");
        }
    }

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_float($input) || is_int($input));

        if (!$this->isMultipleOf($input)) {
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
    private function isMultipleOf(float | int $value): bool
    {
        $value = $value + $this->offset;

        if (is_int($value) && is_int($this->multipleOf) && $value % $this->multipleOf === 0) {
            return true;
        }

        if (is_float($this->multipleOf)) {
            $ofParts = explode('.', (string)$this->multipleOf);
            $precision = strlen($ofParts[1]);
            $of = (int)($ofParts[0] . $ofParts[1]);
            $power = pow(10, $precision);
        } else {
            $of = $this->multipleOf;
            $precision = 0;
            $power = 1;
        }

        if (is_float($value)) {
            $valueParts = explode('.', (string)$value);
            $valueParts[1] ??= '';

            if (strlen($valueParts[1]) > $precision) {
                return false;
            } else {
                $float = str_pad($valueParts[1], $precision, '0');
                $check = (int)($valueParts[0] . $float);
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
