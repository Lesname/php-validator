<?php
declare(strict_types=1);

namespace LessValidatorTest\Number;

use LessValidator\Number\PrecisionValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Number\PrecisionValidator
 */
final class PrecisionValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new PrecisionValidator(3);

        self::assertTrue($validator->validate(0)->isValid());
        self::assertTrue($validator->validate(0.1)->isValid());
        self::assertTrue($validator->validate(0.12)->isValid());
        self::assertTrue($validator->validate(0.123)->isValid());
        self::assertFalse($validator->validate(0.1234)->isValid());
    }
}
