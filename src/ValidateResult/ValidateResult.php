<?php
declare(strict_types=1);

namespace LessValidator\ValidateResult;

use JsonSerializable;

/**
 * @psalm-immutable
 */
interface ValidateResult extends JsonSerializable
{
    public function isValid(): bool;
}
