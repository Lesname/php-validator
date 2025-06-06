<?php
declare(strict_types=1);

namespace LesValidator\Builder;

use LesValidator\Validator;

interface ValidatorBuilder
{
    public function build(): Validator;
}
