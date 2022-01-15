<?php
declare(strict_types=1);

namespace LessValidator\Exception;

/**
 * @psalm-immutable
 */
final class UnexpectedType extends AbstractException
{
    public function __construct(public string $expected, public string $given)
    {
        parent::__construct("Expected {$expected}, given {$given}");
    }
}
