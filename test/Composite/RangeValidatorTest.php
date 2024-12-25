<?php
declare(strict_types=1);

namespace LessValidatorTest\Composite;

use LessValidator\Composite\RangeValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Composite\RangeValidator
 */
class RangeValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidInput
     */
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

    /**
     * @dataProvider getInvalidInput
     */
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
