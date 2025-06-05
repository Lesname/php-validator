<?php
declare(strict_types=1);

namespace LesValidator\Composite;

use Override;
use LesValidator\Validator;
use LesValidator\ValidateResult\ValidateResult;
use LesDocumentor\Type\Document\Composite\Key\Key;
use LesValidator\ValidateResult\Composite\PropertiesValidateResult;

final class PropertyValidator implements Validator
{
    public function __construct(
        private readonly Key $key,
        private readonly Validator $validator,
    ) {}

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input));

        $propertyResults = [];

        /** @psalm-suppress MixedAssignment */
        foreach ($input as $key => $value) {
            if (is_string($key) && $this->key->matches($key)) {
                $propertyResults[$key] = $this->validator->validate($value);
            }
        }

        return new PropertiesValidateResult($propertyResults);
    }
}
