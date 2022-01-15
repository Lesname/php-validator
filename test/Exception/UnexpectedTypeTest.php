<?php
declare(strict_types=1);

namespace LessValidatorTest\Exception;

use LessValidator\Exception\UnexpectedType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Exception\UnexpectedType
 */
final class UnexpectedTypeTest extends TestCase
{
    public function testSetup(): void
    {
        $e = new UnexpectedType('a', 'b');

        self::assertSame('a', $e->expected);
        self::assertSame('b', $e->given);
    }
}
