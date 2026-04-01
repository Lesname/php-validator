<?php

declare(strict_types=1);

namespace LesValidator\Composite;

use Override;
use RuntimeException;
use LesValidator\Validator;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\Composite\PropertiesValidateResult;

final class DiscriminatorValidator implements Validator
{
    /**
     * @param array<string, Validator> $mapping
     */
    public function __construct(
        private readonly string $discriminatorField,
        private readonly string $discriminatorProperty,
        private readonly array $mapping,
    ) {}

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input));

        if (!isset($input[$this->discriminatorField])) {
            throw new RuntimeException();
        }

        $discriminatorValue = $input[$this->discriminatorField];

        if (!is_string($discriminatorValue)) {
            throw new RuntimeException();
        }

        if (!isset($this->mapping[$discriminatorValue])) {
            throw new RuntimeException();
        }

        $validator = $this->mapping[$discriminatorValue];

        return new PropertiesValidateResult(
            [
                $this->discriminatorProperty => $validator->validate($input[$this->discriminatorProperty])
            ],
        );
    }
}
