<?php
declare(strict_types=1);

namespace LesValidatorTest\String;

use LesValidator\String\LengthValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\String\LengthValidator
 */
final class LengthValidatorTest extends TestCase
{
    public function testTooShort(): void
    {
        $validator = new LengthValidator(3, 5);

        $result = $validator->validate('🏴󠁧󠁢󠁥󠁮󠁧󠁿ö');

        self::assertFalse($result->isValid());
        self::assertSame('string.tooShort', $result->code);
    }

    public function testTooLong(): void
    {
        $validator = new LengthValidator(1, 2);

        $result = $validator->validate('föo');

        self::assertFalse($result->isValid());
        self::assertSame('string.tooLong', $result->code);
    }

    public function testValid(): void
    {
        $validator = new LengthValidator(1, 3);

        self::assertTrue($validator->validate('föo')->isValid());
    }
}
