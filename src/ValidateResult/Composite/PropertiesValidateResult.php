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
    /** @var array<string, ValidateResult> */
    public readonly array $properties;

    private readonly bool $valid;

    /**
     * @param iterable<string, ValidateResult> $properties
     */
    public function __construct(iterable $properties)
    {
        $arrayProperties = [];
        $valid = true;

        foreach ($properties as $name => $property) {
            $valid = $valid && $property->isValid();
            $arrayProperties[$name] = $property;
        }

        $this->properties = $arrayProperties;
        $this->valid = $valid;
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
