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

    private float|int|null $minimal = null;

    private float|int|null $maximal = null;

    public static function fromBetween(float|int $minimal, float|int $maximal): self
    {
        return (new self())->withBetween($minimal, $maximal);
    }

    public function isOnlyIntegers(): bool
    {
        return $this->onlyIntegers;
    }

    public function getMinimal(): float|int|null
    {
        return $this->minimal;
    }

    public function getMaximal(): float|int|null
    {
        return $this->maximal;
    }

    public function withBetween(float|int $minimal, float|int $maximal): self
    {
        $clone = clone $this;
        $clone->minimal = $minimal;
        $clone->maximal = $maximal;

        return $clone;
    }

    public function withMinimum(float|int $minimal): self
    {
        $clone = clone $this;
        $clone->minimal = $minimal;

        return $clone;
    }

    public function withMaximal(float|int $maximal): self
    {
        $clone = clone $this;
        $clone->maximal = $maximal;

        return $clone;
    }

    public function withOnlyIntegers(bool $onlyIntegers = true): self
    {
        $clone = clone $this;
        $clone->onlyIntegers = $onlyIntegers;

        return $clone;
    }

    public function build(): Validator
    {
        if ($this->minimal === null) {
            throw new RuntimeException("No minimal");
        }

        if ($this->maximal === null) {
            throw new RuntimeException("No maximal");
        }

        return new ChainValidator(
            [
                $this->onlyIntegers
                    ? TypeValidator::integer()
                    : TypeValidator::number(),
                new BetweenValidator($this->minimal, $this->maximal),
            ],
        );
    }
}
