<?php
declare(strict_types=1);

namespace LesValidatorTest\Number;

use LesValidator\Number\MultipleOfValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(\LesValidator\Number\MultipleOfValidator::class)]
class MultipleOfValidatorTest extends TestCase
{
    #[DataProvider('getValidData')]
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
