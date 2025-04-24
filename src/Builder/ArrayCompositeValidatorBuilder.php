<?php
declare(strict_types=1);

namespace LesValidator\Builder;

use RuntimeException;
use LesValidator\Validator;
use LesValidator\TypeValidator;
use LesValidator\ChainValidator;
use LesValidator\Composite\PropertyKeysValidator;
use LesValidator\Composite\PropertyValuesValidator;

/**
 * @psalm-immutable
 */
final class ArrayCompositeValidatorBuilder implements ValidatorBuilder
{
    /**
     * @param array<string, Validator>|null $valueValidators
     */
    public function __construct(private readonly ?array $valueValidators = null)
    {}

    /**
     * @param array<string, Validator> $validators
     */
    public function withArrayCompositeValidators(array $validators): self
    {
        return new self($validators);
    }

    /**
     * @param array<string, Validator> $valueValidators
     */
    public function withValueValidators(array $valueValidators): self
    {
        return new self($valueValidators);
    }

    public function build(): Validator
    {
        if ($this->valueValidators === null) {
            throw new RuntimeException("No value validators have been configured");
        }

        return new ChainValidator(
            [
                TypeValidator::composite(),
                new PropertyKeysValidator(array_keys($this->valueValidators)),
                new PropertyValuesValidator($this->valueValidators),
            ],
        );
    }
}
