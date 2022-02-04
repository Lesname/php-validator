<?php
declare(strict_types=1);

namespace LessValidator\Exception;

/**
 * @psalm-immutable
 */
final class UnexpectedType extends AbstractException
{
    public function __construct(public readonly string $expected, public readonly string $given)
    {
        parent::__construct("Expected {$expected}, given {$given}");
    }
}
