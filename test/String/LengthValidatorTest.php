<?php
declare(strict_types=1);

namespace LessValidatorTest\String;

use LessValidator\String\LengthValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\String\LengthValidator
 */
final class LengthValidatorTest extends TestCase
{
    public function testTooShort(): void
    {
        $validator = new LengthValidator(3, 5);

        self::assertFalse($validator->validate('fö')->isValid());
    }

    public function testTooLong(): void
    {
        $validator = new LengthValidator(1, 2);

        self::assertFalse($validator->validate('föo')->isValid());
    }

    public function testValid(): void
    {
        $validator = new LengthValidator(1, 3);

        self::assertTrue($validator->validate('föo')->isValid());
    }
}
