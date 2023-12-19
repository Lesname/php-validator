<?php
declare(strict_types=1);

namespace LessValidatorTest\Number;

use LessValidator\Number\PrecisionValidator;
use LessValidator\Number\MultipleOfValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Number\MultipleOfValidator
 */
class MultipleOfValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidData
     */
    public function testValidate(int|float $multipleOf, int|float $value): void
    {
        $validator = new MultipleOfValidator($multipleOf);

        self::assertTrue($validator->validate($value)->isValid());
    }

    /**
     * @return array<mixed>
     */
    public static function getValidData(): array
    {
        return [
            [1, 5],
            [2, 6],
            [3, -9],
            [.1, .5],
            [.4, 2],
            [.4, 1.6],
        ];
    }
}
