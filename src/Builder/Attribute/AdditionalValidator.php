<?php
declare(strict_types=1);

namespace LessValidator\Builder\Attribute;

use Attribute;
use LessValidator\Validator;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
final class AdditionalValidator
{
    /**
     * @param class-string<Validator> $validator
     */
    public function __construct(public readonly string $validator)
    {}
}
