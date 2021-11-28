<?php
declare(strict_types=1);

namespace LessValidatorTest\Number;

use LessValidator\Number\BetweenValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Number\BetweenValidator
 */
final class BetweenValidatorTest extends TestCase
{
    public function testTooLittle(): void
    {
        $validator = new BetweenValidator(1, 5.15);

        self::assertFalse($validator->validate(.9)->isValid());
        self::assertFalse($validator->validate(-2)->isValid());
    }

    public function testTooGreat(): void
    {
        $validator = new BetweenValidator(1, 5.15);

        self::assertFalse($validator->validate(5.16)->isValid());
        self::assertFalse($validator->validate(6)->isValid());
    }

    public function testValid(): void
    {
        $validator = new BetweenValidator(1, 5.15);

        self::assertTrue($validator->validate(2)->isValid());
    }
}
