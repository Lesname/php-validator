<?php
declare(strict_types=1);

namespace LessValidatorTest;

use LessValidator\TypeValidator;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @covers \LessValidator\TypeValidator
 */
final class TypeValidatorTest extends TestCase
{
    public function testBoolean(): void
    {
        $validator = new TypeValidator(TypeValidator::BOOLEAN);

        self::assertTrue($validator->validate(true)->isValid());

        self::assertFalse($validator->validate([])->isValid());
        self::assertFalse($validator->validate([1])->isValid());
        self::assertFalse($validator->validate(['fiz' => 1])->isValid());
        self::assertFalse($validator->validate(1)->isValid());
        self::assertFalse($validator->validate(1.2)->isValid());
        self::assertFalse($validator->validate('foo')->isValid());
    }

    public function testCollection(): void
    {
        $validator = new TypeValidator(TypeValidator::COLLECTION);

        self::assertTrue($validator->validate([])->isValid());
        self::assertTrue($validator->validate([1])->isValid());

        self::assertFalse($validator->validate(true)->isValid());
        self::assertFalse($validator->validate(['fiz' => 1])->isValid());
        self::assertFalse($validator->validate(1)->isValid());
        self::assertFalse($validator->validate(1.2)->isValid());
        self::assertFalse($validator->validate('foo')->isValid());
    }

    public function testComposite(): void
    {
        $validator = new TypeValidator(TypeValidator::COMPOSITE);

        self::assertTrue($validator->validate([])->isValid());
        self::assertTrue($validator->validate(['fiz' => 1])->isValid());

        self::assertFalse($validator->validate(true)->isValid());
        self::assertFalse($validator->validate([1])->isValid());
        self::assertFalse($validator->validate(1)->isValid());
        self::assertFalse($validator->validate(1.2)->isValid());
        self::assertFalse($validator->validate('foo')->isValid());
    }

    public function testInteger(): void
    {
        $validator = new TypeValidator(TypeValidator::INTEGER);

        self::assertTrue($validator->validate(1)->isValid());

        self::assertFalse($validator->validate([])->isValid());
        self::assertFalse($validator->validate(true)->isValid());
        self::assertFalse($validator->validate([1])->isValid());
        self::assertFalse($validator->validate(['fiz' => 1])->isValid());
        self::assertFalse($validator->validate(1.2)->isValid());
        self::assertFalse($validator->validate('foo')->isValid());
    }

    public function testNumber(): void
    {
        $validator = new TypeValidator(TypeValidator::NUMBER);

        self::assertTrue($validator->validate(1)->isValid());
        self::assertTrue($validator->validate(1.2)->isValid());

        self::assertFalse($validator->validate([])->isValid());
        self::assertFalse($validator->validate(true)->isValid());
        self::assertFalse($validator->validate([1])->isValid());
        self::assertFalse($validator->validate(['fiz' => 1])->isValid());
        self::assertFalse($validator->validate('foo')->isValid());
    }

    public function testString(): void
    {
        $validator = new TypeValidator(TypeValidator::STRING);

        self::assertTrue($validator->validate('foo')->isValid());

        self::assertFalse($validator->validate([])->isValid());
        self::assertFalse($validator->validate(true)->isValid());
        self::assertFalse($validator->validate([1])->isValid());
        self::assertFalse($validator->validate(['fiz' => 1])->isValid());
        self::assertFalse($validator->validate(1)->isValid());
        self::assertFalse($validator->validate(1.2)->isValid());
    }

    public function testUnknown(): void
    {
        $this->expectException(Throwable::class);

        $validator = new TypeValidator('foo');

        $validator->validate('foo');
    }
}
