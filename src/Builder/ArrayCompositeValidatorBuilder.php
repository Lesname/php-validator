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
     * @var array<string, Validator>|null
     */
    private ?array $arrayCompositeValidators = null;

    /**
     * @param array<string, Validator> $validators
     */
    public static function fromArrayCompositeValidators(array $validators): self
    {
        return (new self())->withArrayCompositeValidators($validators);
    }

    /**
     * @param array<string, Validator> $validators
     */
    public function withArrayCompositeValidators(array $validators): self
    {
        $clone = clone $this;
        $clone->arrayCompositeValidators = $validators;

        return $clone;
    }

    public function build(): Validator
    {
        if ($this->arrayCompositeValidators === null) {
            throw new RuntimeException("No array composite validators have been configured");
        }

        return new ChainValidator(
            [
                TypeValidator::composite(),
                new PropertyKeysValidator(array_keys($this->arrayCompositeValidators)),
                new PropertyValuesValidator($this->arrayCompositeValidators),
            ],
        );
    }
}
