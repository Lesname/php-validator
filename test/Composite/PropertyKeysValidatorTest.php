<?php
declare(strict_types=1);

namespace LessValidatorTest\Composite;

use LessValidator\Composite\PropertyKeysValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Composite\PropertyKeysValidator
 */
final class PropertyKeysValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $validator = new PropertyKeysValidator(['fiz', 'foo']);

        $result = $validator->validate(
            [
                'fiz' => 1,
                'foo' => 2,
            ],
        );

        self::assertTrue($result->isValid());
    }

    public function testInvalid(): void
    {
        $validator = new PropertyKeysValidator(['fiz', 'foo']);

        $result = $validator->validate(
            [
                'fiz' => 1,
                'bar' => 2,
            ],
        );

        self::assertFalse($result->isValid());
    }
}
