<?php

declare(strict_types=1);

namespace LesValidator\Builder;

use Override;
use RuntimeException;
use LesValidator\Validator;
use LesValidator\TypeValidator;
use LesValidator\ChainValidator;
use LesValidator\Number\BetweenValidator;

/**
 * @deprecated will be dropped
 */
final class NumericValidatorBuilder implements ValidatorBuilder
{
    /**
     * @psalm-mutation-free
     */
    public function __construct(
        private readonly bool $onlyIntegers = false,
        private readonly float|int|null $minimal = null,
        private readonly float|int|null $maximal = null,
    ) {}

    /**
     * @psalm-mutation-free
     */
    public function isOnlyIntegers(): bool
    {
        return $this->onlyIntegers;
    }

    /**
     * @psalm-mutation-free
     */
    public function getMinimal(): float|int|null
    {
        return $this->minimal;
    }

    /**
     * @psalm-mutation-free
     */
    public function getMaximal(): float|int|null
    {
        return $this->maximal;
    }

    /**
     * @psalm-mutation-free
     */
    public function withBetween(float|int $minimal, float|int $maximal): self
    {
        return new self($this->onlyIntegers, $minimal, $maximal);
    }

    /**
     * @psalm-mutation-free
     */
    public function withMinimum(float|int $minimal): self
    {
        return new self($this->onlyIntegers, $minimal, $this->maximal);
    }

    /**
     * @psalm-mutation-free
     */
    public function withMaximal(float|int $maximal): self
    {
        return new self($this->onlyIntegers, $this->minimal, $maximal);
    }

    /**
     * @psalm-mutation-free
     */
    public function withOnlyIntegers(bool $onlyIntegers = true): self
    {
        return new self($onlyIntegers, $this->minimal, $this->maximal);
    }

    /**
     * @psalm-mutation-free
     */
    #[Override]
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
