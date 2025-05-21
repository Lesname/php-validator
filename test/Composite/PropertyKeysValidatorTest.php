<?php
declare(strict_types=1);

namespace LesValidatorTest\Composite;

use LesValidator\Composite\PropertyKeysValidator;
use PHPUnit\Framework\TestCase;
use LesDocumentor\Type\Document\Composite\Key\AnyKey;
use LesDocumentor\Type\Document\Composite\Key\RegexKey;
use LesDocumentor\Type\Document\Composite\Key\ExactKey;

/**
 * @covers \LesValidator\Composite\PropertyKeysValidator
 */
final class PropertyKeysValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $validator = new PropertyKeysValidator(['fiz', new ExactKey('foo')]);

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
        $validator = new PropertyKeysValidator(['fiz', new ExactKey('foo')]);

        $result = $validator->validate(
            [
                'fiz' => 1,
                'bar' => 2,
            ],
        );

        self::assertFalse($result->isValid());
    }

    public function testRegexKey(): void
    {
        $validator = new PropertyKeysValidator([new RegexKey('^f')]);

        self::assertTrue($validator->validate(['fiz' => 1])->isValid());
        self::assertTrue($validator->validate(['foo' => 1])->isValid());
        self::assertFalse($validator->validate(['bar' => 1])->isValid());
        self::assertFalse($validator->validate(['zif' => 1])->isValid());
    }

    public function testAnyKey(): void
    {
        $validator = new PropertyKeysValidator([new AnyKey()]);

        self::assertTrue($validator->validate(['fiz' => 1])->isValid());
        self::assertTrue($validator->validate(['bar' => 1])->isValid());
    }
}
