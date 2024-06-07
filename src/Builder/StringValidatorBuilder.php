<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use RuntimeException;
use LessValidator\Validator;
use LessValidator\TypeValidator;
use LessValidator\ChainValidator;
use LessValidator\String\LengthValidator;

/**
 * @psalm-immutable
 */
final class StringValidatorBuilder implements ValidatorBuilder
{
    private int|null $minLength = null;

    private int|null $maxLength = null;

    public static function fromBetween(int $minLength, int $maxLength): self
    {
        return (new self())->withBetween($minLength, $maxLength);
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    public function withBetween(int $minLength, int $maxLength): self
    {
        $clone = clone $this;
        $clone->minLength = $minLength;
        $clone->maxLength = $maxLength;

        return $clone;
    }

    public function withMinLength(int $minLength): self
    {
        $clone = clone $this;
        $clone->minLength = $minLength;

        return $clone;
    }

    public function withMaxLength(int $maxLength): self
    {
        $clone = clone $this;
        $clone->maxLength = $maxLength;

        return $clone;
    }

    public function build(): Validator
    {
        if ($this->minLength === null) {
            throw new RuntimeException("No minimum length");
        }

        if ($this->maxLength === null) {
            throw new RuntimeException("No maximum length");
        }

        return new ChainValidator(
            [
                TypeValidator::string(),
                new LengthValidator($this->minLength, $this->maxLength),
            ],
        );
    }
}
