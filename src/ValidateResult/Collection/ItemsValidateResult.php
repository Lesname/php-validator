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
    /** @var array<int, ValidateResult> */
    public readonly array $items;

    private readonly bool $valid;

    /**
     * @param iterable<int, ValidateResult> $items
     */
    public function __construct(iterable $items)
    {
        $arrayItems = [];
        $valid = true;

        foreach ($items as $item) {
            $valid = $valid && $item->isValid();
            $arrayItems[] = $item;
        }

        $this->items = $arrayItems;
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
        return ['items' => $this->items];
    }
}
