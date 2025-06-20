<?php
declare(strict_types=1);

namespace LesValidator;

use Override;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;

final class ChainValidator implements Validator
{
    /** @var array<Validator> */
    public readonly array $validators;

    /** @param iterable<Validator> $validators */
    public function __construct(iterable $validators)
    {
        $validatorsArray = [];

        foreach ($validators as $validator) {
            $validatorsArray[] = $validator;
        }

        $this->validators = $validatorsArray;
    }

    public static function chain(Validator ...$validators): self
    {
        return new self($validators);
    }

    #[Override]
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
