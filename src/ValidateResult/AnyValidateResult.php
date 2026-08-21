<?php

declare(strict_types=1);

namespace LesValidator\ValidateResult;

use Override;

/**
 * @psalm-immutable
 */
final class AnyValidateResult implements ValidateResult
{
    private readonly bool $valid;

    /**
     * @param list<ValidateResult> $items
     *
     * @psalm-mutation-free
     */
    public function __construct(public readonly array $items)
    {
        $this->valid = array_any(
            $this->items,
            static fn (ValidateResult $result): bool => $result->isValid(),
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
        return ['any' => $this->items];
    }
}
