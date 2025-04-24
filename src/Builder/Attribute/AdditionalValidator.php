<?php
declare(strict_types=1);

namespace LesValidator\Builder\Attribute;

use Attribute;
use LesValidator\Validator;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
final class AdditionalValidator
{
    /**
     * @param class-string<Validator> $validator
     */
    public function __construct(public readonly string $validator)
    {}
}
