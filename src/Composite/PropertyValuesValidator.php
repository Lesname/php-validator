<?php
declare(strict_types=1);

namespace LessValidator\Composite;

use LessValidator\ValidateResult\Composite\PropertiesValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\Validator;

/**
 * @psalm-immutable
 */
final class PropertyValuesValidator implements Validator
{
    /** @var array<string, Validator> */
    public readonly array $propertyValueValidators;

    /** @param iterable<string, Validator> $propertyValueValidators */
    public function __construct(iterable $propertyValueValidators)
    {
        $propertyValueValidatorsArray = [];

        foreach ($propertyValueValidators as $name => $propertyValueValidator) {
            $propertyValueValidatorsArray[$name] = $propertyValueValidator;
        }

        $this->propertyValueValidators = $propertyValueValidatorsArray;
    }

    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input));

        $propertyValueValidators = $this->propertyValueValidators;

        return new PropertiesValidateResult(
            (
                /** @psalm-pure */
                function (array $input) use ($propertyValueValidators): iterable {
                    foreach ($propertyValueValidators as $name => $propertyValueValidator) {
                        yield $name => $propertyValueValidator->validate($input[$name] ?? null);
                    }
                }
            )($input),
        );
    }
}
