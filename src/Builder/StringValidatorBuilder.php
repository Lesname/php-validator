<?php
declare(strict_types=1);

namespace LesValidator\Builder;

use Override;
use RuntimeException;
use LesValidator\Validator;
use LesValidator\TypeValidator;
use LesValidator\ChainValidator;
use LesValidator\String\LengthValidator;

final class StringValidatorBuilder implements ValidatorBuilder
{
    public function __construct(
        private readonly ?int $minLength = null,
        private readonly ?int $maxLength = null,
    ) {}

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
        return new self($minLength, $maxLength);
    }

    public function withMinLength(int $minLength): self
    {
        return new self($minLength, $this->maxLength);
    }

    public function withMaxLength(int $maxLength): self
    {
        return new self($this->minLength, $maxLength);
    }

    #[Override]
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
