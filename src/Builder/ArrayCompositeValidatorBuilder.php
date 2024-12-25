<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use RuntimeException;
use LessValidator\Validator;
use LessValidator\TypeValidator;
use LessValidator\ChainValidator;
use LessValidator\Composite\PropertyKeysValidator;
use LessValidator\Composite\PropertyValuesValidator;

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
     * @deprecated use constructor
     *
     * @param array<string, Validator> $validators
     */
    public static function fromArrayCompositeValidators(array $validators): self
    {
        return new self($validators);
    }

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
