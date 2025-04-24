<?php
declare(strict_types=1);

namespace LesValidator\Builder;

use LesValidator\Validator;

/**
 * @psalm-immutable
 */
interface ValidatorBuilder
{
    public function build(): Validator;
}
