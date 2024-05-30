<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use RuntimeException;
use LessValidator\Validator;
use LessValidator\TypeValidator;
use LessValidator\ChainValidator;
use LessValidator\Number\BetweenValidator;

/**
 * @psalm-immutable
 */
final class NumericValidatorBuilder implements ValidatorBuilder
{
    private bool $onlyIntegers = false;

    private float|int|null $min = null;

    private float|int|null $max = null;

    public static function fromBetween(float|int $min, float|int $max): self
    {
        return (new self())->withBetween($min, $max);
    }

    public function withBetween(float|int $min, float|int $max): self
    {
        $clone = clone $this;
        $clone->min = $min;
        $clone->max = $max;

        return $clone;
    }

    public function withOnlyIntegers(): self
    {
        $clone = clone $this;
        $clone->onlyIntegers = true;

        return $clone;
    }

    public function build(): Validator
    {
        if ($this->min === null) {
            throw new RuntimeException("No minimum");
        }

        if ($this->max === null) {
            throw new RuntimeException("No maximum");
        }

        return new ChainValidator(
            [
                $this->onlyIntegers
                    ? TypeValidator::integer()
                    : TypeValidator::number(),
                new BetweenValidator($this->min, $this->max),
            ],
        );
    }
}
