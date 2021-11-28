<?php
declare(strict_types=1);

namespace LessValidatorTest\String;

use LessValidator\String\OptionsValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\String\OptionsValidator
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
