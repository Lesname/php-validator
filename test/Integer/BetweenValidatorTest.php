<?php
declare(strict_types=1);

namespace LessValidatorTest\Integer;

use LessValidator\Integer\BetweenValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Integer\BetweenValidator
 */
final class BetweenValidatorTest extends TestCase
{
    public function testTooLittle(): void
    {
        $validator = new BetweenValidator(1, 5);

        self::assertFalse($validator->validate(-2)->isValid());
    }

    public function testTooGreat(): void
    {
        $validator = new BetweenValidator(1, 5);

        self::assertFalse($validator->validate(6)->isValid());
    }

    public function testValid(): void
    {
        $validator = new BetweenValidator(1, 5);

        self::assertTrue($validator->validate(2)->isValid());
    }
}
