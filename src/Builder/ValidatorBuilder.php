<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use LessValidator\Validator;

/**
 * @psalm-immutable
 */
interface ValidatorBuilder
{
    public function build(): Validator;
}
