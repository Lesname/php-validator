<?php

declare(strict_types=1);

namespace LesValidator;

use Override;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;

final class ChainValidator implements Validator
{
    /**
     * @param array<Validator> $validators
     *
     * @psalm-pure
     */
    public function __construct(public readonly array $validators)
    {}

    /**
     * @psalm-pure
     */
    public static function chain(Validator ...$validators): self
    {
        return new self($validators);
    }

    /**
     * @psalm-impure
     */
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
