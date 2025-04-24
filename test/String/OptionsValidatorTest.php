<?php
declare(strict_types=1);

namespace LesValidatorTest\String;

use LesValidator\String\OptionsValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\String\OptionsValidator
 */
final class OptionsValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $validator = new OptionsValidator(['fiz', 'biz']);

        self::assertTrue($validator->validate('fiz')->isValid());
    }

    public function testInvalid(): void
    {
        $validator = new OptionsValidator(['fiz', 'biz']);

        self::assertFalse($validator->validate('foo')->isValid());
    }
}
