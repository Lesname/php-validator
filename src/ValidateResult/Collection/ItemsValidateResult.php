<?php
declare(strict_types=1);

namespace LessValidator\ValidateResult\Collection;

use LessValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
final class ItemsValidateResult implements ValidateResult
{
    /** @var array<int, ValidateResult> */
    public array $items = [];

    private bool $valid = true;

    /**
     * @param iterable<int, ValidateResult> $items
     */
    public function __construct(iterable $items)
    {
        foreach ($items as $item) {
            $this->valid = $this->valid && $item->isValid();
            $this->items[] = $item;
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
        return ['items' => $this->items];
    }
}
