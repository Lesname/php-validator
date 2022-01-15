<?php
declare(strict_types=1);

namespace LessValidator\Composite;

use LessValidator\Exception\UnexpectedType;
use LessValidator\ValidateResult\Composite\PropertiesValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\Validator;

/**
 * @psalm-immutable
 */
final class PropertyValuesValidator implements Validator
{
    /** @var array<string, Validator> */
    public array $proprtyValueValidators = [];

    /** @param iterable<string, Validator> $proprtyValueValidators */
    public function __construct(iterable $proprtyValueValidators)
    {
        foreach ($proprtyValueValidators as $name => $proprtyValueValidator) {
            $this->proprtyValueValidators[$name] = $proprtyValueValidator;
        }
    }

    public function validate(mixed $input): ValidateResult
    {
        return new PropertiesValidateResult(
            (function (mixed $input): iterable {
                assert(is_array($input), new UnexpectedType('array', get_debug_type($input)));

                foreach ($this->proprtyValueValidators as $name => $proprtyValueValidator) {
                    yield $name => $proprtyValueValidator->validate($input[$name] ?? null);
                }
            })($input),
        );
    }
}
