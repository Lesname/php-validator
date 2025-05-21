<?php
declare(strict_types=1);

namespace LesValidator\Composite;

use Override;
use LesValidator\ValidateResult\Composite\PropertiesValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\Validator;

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

    #[Override]
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
