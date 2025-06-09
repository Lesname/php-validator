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
    private readonly int $precision;

    public function __construct(
        private readonly float|int $multipleOf,
        private readonly float|int $offset = 0,
        ?int $precision = null,
    ) {
        if ($multipleOf <= 0) {
            throw new RuntimeException("Multiple of must be >0, gotten '{$multipleOf}'");
        }

        $this->precision = $precision ?? (int)ini_get('precision');
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

    private function isMultipleOf(float|int $value): bool
    {
        $value = $value + $this->offset;

        if (is_int($value) && is_int($this->multipleOf) && $value % $this->multipleOf === 0) {
            return true;
        }

        $remainder = rtrim(
            bcmod(
                $this->floatToString($value),
                $this->floatToString($this->multipleOf),
                $this->precision,
            ),
            '.0',
        );

        return $remainder === '';
    }

    private function floatToString(float $float): string
    {
        return rtrim(
            rtrim(
                sprintf(
                    '%.' . $this->precision . 'f',
                    $float
                ),
                '0'
            ),
            '.'
        );
    }
}
