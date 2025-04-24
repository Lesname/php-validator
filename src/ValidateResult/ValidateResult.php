<?php
declare(strict_types=1);

namespace LesValidator\ValidateResult;

use JsonSerializable;

/**
 * @psalm-immutable
 */
interface ValidateResult extends JsonSerializable
{
    public function isValid(): bool;
}
