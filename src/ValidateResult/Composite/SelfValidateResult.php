<?php
declare(strict_types=1);

namespace LesValidator\ValidateResult\Composite;

use Override;
use LesValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
final class SelfValidateResult implements ValidateResult
{
    public function __construct(public readonly ValidateResult $self)
    {}

    #[Override]
    public function isValid(): bool
    {
        return $this->self->isValid();
    }

    /**
     * @return array<mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return ['self' => $this->self];
    }
}
