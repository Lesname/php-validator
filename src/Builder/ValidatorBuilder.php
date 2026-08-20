<?php

declare(strict_types=1);

namespace LesValidator\Builder;

use LesValidator\Validator;

/**
 * @todo move this to own package
 */
interface ValidatorBuilder
{
    public function build(): Validator;
}
