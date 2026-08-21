<?php

declare(strict_types=1);

namespace LesValidator\ValidateResult;

use JsonSerializable;

/**
 * @psalm-immutable
 */
interface ValidateResult extends JsonSerializable
{
    /**
     * @psalm-mutation-free
     */
    public function isValid(): bool;
}
