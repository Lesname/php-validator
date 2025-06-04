<?php
declare(strict_types=1);

namespace LesValidator;

use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\AnyValidateResult;

/**
 * @psalm-immutable
 */
final class AnyValidator implements Validator
{
    /** @var array<Validator> */
    public readonly array $subValidators;

    /** @param iterable<Validator> $validators */
    public function __construct(iterable $validators)
    {
        $validatorsArray = [];

        foreach ($validators as $validator) {
            $validatorsArray[] = $validator;
        }

        $this->subValidators = $validatorsArray;
    }

    public function validate(mixed $input): ValidateResult
    {
        $results = [];

        foreach ($this->subValidators as $subValidator) {
            $result = $subValidator->validate($input);

            if ($result->isValid()) {
                return $result;
            }

            $results[] = $result;
        }

        return new AnyValidateResult($results);
    }
}
