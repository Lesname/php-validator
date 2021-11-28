<?php
declare(strict_types=1);

namespace LessValidatorTest\Collection;

use LessValidator\Collection\SizeValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Collection\SizeValidator
 */
final class SizeValidatorTest extends TestCase
{
    public function testTooSmall(): void
    {
        $validator = new SizeValidator(5, PHP_INT_MAX);

        self::assertFalse($validator->validate([1, 2, 3])->isValid());
    }

    public function testTooLarge(): void
    {
        $validator = new SizeValidator(1, 2);

        self::assertFalse($validator->validate([1, 2, 3])->isValid());
    }

    public function testValid(): void
    {
        $validator = new SizeValidator(1, PHP_INT_MAX);

        self::assertTrue($validator->validate([1, 2, 3])->isValid());
    }
}
