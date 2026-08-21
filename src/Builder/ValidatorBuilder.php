<?php

declare(strict_types=1);

namespace LesValidator\Builder;

use LesValidator\Validator;

/**
 * @todo move this to own package
 *
 * @psalm-mutable
 */
interface ValidatorBuilder
{
    /**
     * @psalm-impure
     */
    public function build(): Validator;
}
