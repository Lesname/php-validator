<?php

declare(strict_types=1);

namespace LesValidator\ValidateResult\Collection;

use Override;
use LesValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
final class ItemsValidateResult implements ValidateResult
{
    private readonly bool $valid;

    /**
     * @param list<ValidateResult> $items
     *
     * @psalm-mutation-free
     */
    public function __construct(public readonly array $items)
    {
        $this->valid = array_all($items, fn (ValidateResult $item) => $item->isValid());
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
        return ['items' => $this->items];
    }
}
