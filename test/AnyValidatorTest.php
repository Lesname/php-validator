<?php
declare(strict_types=1);

namespace LesValidatorTest;

use LesValidator\AnyValidator;
use PHPUnit\Framework\TestCase;
use LesValidator\TypeValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AnyValidator::class)]
class AnyValidatorTest extends TestCase
{
    #[DataProvider('getValidInput')]
    public function testValid(mixed $input): void
    {
        $validator = new AnyValidator(
            [
                TypeValidator::string(),
                TypeValidator::integer(),
            ],
        );

        self::assertTrue($validator->validate($input)->isValid());
    }

    public static function getValidInput(): array
    {
        return [
            'string' => ['foo'],
            'int' => [1],
        ];
    }


    #[DataProvider('getInvalidInput')]
    public function testInvalid(mixed $input): void
    {
        $validator = new AnyValidator(
            [
                TypeValidator::string(),
                TypeValidator::integer(),
            ],
        );

        self::assertFalse($validator->validate($input)->isValid());
    }

    public static function getInvalidInput(): array
    {
        return [
            'float' => [123.321],
            'bool' => [true],
        ];
    }
}
