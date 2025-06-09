<?php
declare(strict_types=1);

namespace LesValidatorTest\Number;

use LesValidator\Number\MultipleOfValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(MultipleOfValidator::class)]
class MultipleOfValidatorTest extends TestCase
{
    #[DataProvider('getValidData')]
    public function testValidate(int|float $multipleOf, int|float $offset, int|float $value): void
    {
        $validator = new MultipleOfValidator($multipleOf, $offset);

        self::assertTrue($validator->validate($value)->isValid());
    }

    /**
     * @return array<mixed>
     */
    public static function getValidData(): array
    {
        return [
            [1, 0, 0],
            [1, 0, 5],
            [2, 0, 6],
            [2, 1, 5],
            [3, 0, -9],
            [.1, 0, .5],
            [.4, 0, 2],
            [.4, 0, 1.6],
            [.4, .2, 1.8],
            'eFloat' => [.000_01, 0, .000_05],
        ];
    }

    #[DataProvider('getInvalidData')]
    public function testInvalidValidate(int|float $multipleOf, int|float $offset, int|float $value): void
    {
        $validator = new MultipleOfValidator($multipleOf, $offset);

        self::assertFalse($validator->validate($value)->isValid());
    }

    /**
     * @return array<mixed>
     */
    public static function getInvalidData(): array
    {
        return [
            [1, 0, 1.4],
            [2, 0, 5],
            [2, 1, 4],
            [3, 0, -8],
            [.2, 0, .5],
            [.4, 0, 2.1],
            [.4, .2, 1.6],
            'eFloat' => [.000_02, 0, .000_05],
        ];
    }
}
