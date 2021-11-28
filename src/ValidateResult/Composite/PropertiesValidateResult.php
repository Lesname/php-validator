<?php
declare(strict_types=1);

namespace LessValidator\ValidateResult\Composite;

use LessValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
final class PropertiesValidateResult implements ValidateResult
{
    /** @var array<string, ValidateResult> */
    public array $properties = [];

    private bool $valid = true;

    /**
     * @param iterable<string, ValidateResult> $properties
     */
    public function __construct(iterable $properties)
    {
        foreach ($properties as $name => $property) {
            $this->valid = $this->valid && $property->isValid();
            $this->properties[$name] = $property;
        }
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return ['properties' => $this->properties];
    }
}
