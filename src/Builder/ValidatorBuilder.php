<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use LessValidator\Validator;

interface ValidatorBuilder
{
    public function build(): Validator;
}
