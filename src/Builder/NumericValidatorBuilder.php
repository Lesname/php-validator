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
    public function __construct(
        private readonly bool $onlyIntegers = false,
        private readonly float|int|null $minimal = null,
        private readonly float|int|null $maximal = null,
    ) {}

    /**
     * @deprecated use constructor
     */
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
        return new self($this->onlyIntegers, $minimal, $maximal);
    }

    public function withMinimum(float|int $minimal): self
    {
        return new self($this->onlyIntegers, $minimal, $this->maximal);
    }

    public function withMaximal(float|int $maximal): self
    {
        return new self($this->onlyIntegers, $this->minimal, $maximal);
    }

    public function withOnlyIntegers(bool $onlyIntegers = true): self
    {
        return new self($onlyIntegers, $this->minimal, $this->maximal);
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
