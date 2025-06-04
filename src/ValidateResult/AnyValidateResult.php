<?php
declare(strict_types=1);

namespace LesValidator\ValidateResult;

use Override;

/**
 * @psalm-immutable
 */
final class AnyValidateResult implements ValidateResult
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
        $valid = false;

        foreach ($items as $item) {
            $valid = $valid || $item->isValid();
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
        return ['any' => $this->items];
    }
}
