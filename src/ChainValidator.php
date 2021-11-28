<?php
declare(strict_types=1);

namespace LessValidator;

use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;

/**
 * @psalm-immutable
 */
final class ChainValidator implements Validator
{
    /** @var array<Validator> */
    public array $validators = [];

    /** @param iterable<Validator> $validators */
    public function __construct(iterable $validators)
    {
        foreach ($validators as $validator) {
            $this->validators[] = $validator;
        }
    }

    public function validate(mixed $input): ValidateResult
    {
        foreach ($this->validators as $validator) {
            $result = $validator->validate($input);

            if (!$result->isValid()) {
                return $result;
            }
        }

        return new ValidValidateResult();
    }
}
