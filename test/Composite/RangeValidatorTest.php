<?php
declare(strict_types=1);

namespace LesValidatorTest\Composite;

use LesValidator\Composite\RangeValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @covers \LesValidator\Composite\RangeValidator
 */
class RangeValidatorTest extends TestCase
{
    #[DataProvider('getValidInput')]
    public function testValid(mixed $input): void
    {
        self::assertTrue((new RangeValidator())->validate($input)->isValid());
    }

    public static function getValidInput(): array
    {
        return [
            'minLowerThanMax' => [
                [
                    'min' => 1,
                    'max' => 2,
                ],
            ],
            'minEqualsMax' => [
                [
                    'min' => 2,
                    'max' => 2,
                ],
            ],
            'noMin' => [
                [
                    'max' => 2,
                ],
            ],
            'noMax' => [
                [
                    'min' => 1,
                ],
            ],
        ];
    }

    #[DataProvider('getInvalidInput')]
    public function testInvalid(mixed $input): void
    {
        self::assertFalse((new RangeValidator())->validate($input)->isValid());
    }

    public static function getInvalidInput(): array
    {
        return [
            'minGreaterThanMax' => [
                [
                    'min' => 3,
                    'max' => 2,
                ],
            ],
        ];
    }
}
