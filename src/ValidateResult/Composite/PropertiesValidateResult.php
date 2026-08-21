<?php

declare(strict_types=1);

namespace LesValidator\ValidateResult\Composite;

use Override;
use LesValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
final class PropertiesValidateResult implements ValidateResult
{
    private readonly bool $valid;

    /**
     * @param array<string, ValidateResult> $properties
     *
     * @psalm-mutation-free
     */
    public function __construct(public readonly array $properties)
    {
        $this->valid = array_all(
            $this->properties,
            static fn (ValidateResult $property): bool => $property->isValid(),
        );
    }

    #[Override]
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return array<mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return ['properties' => $this->properties];
    }
}
