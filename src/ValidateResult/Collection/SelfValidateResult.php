<?php
declare(strict_types=1);

namespace LessValidator\ValidateResult\Collection;

use LessValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
final class SelfValidateResult implements ValidateResult
{
    public function __construct(public readonly ValidateResult $self)
    {}

    public function isValid(): bool
    {
        return $this->self->isValid();
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return ['self' => $this->self];
    }
}
