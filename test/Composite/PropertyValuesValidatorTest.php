<?php
declare(strict_types=1);

namespace LesValidatorTest\Composite;

use LesValidator\Composite\PropertyValuesValidator;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\Composite\PropertyValuesValidator
 */
final class PropertyValuesValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $fooResult = $this->createMock(ValidateResult::class);
        $fooResult->method('isValid')->willReturn(true);
        $fooValidator = $this->createMock(Validator::class);
        $fooValidator->expects(self::once())->method('validate')->with('foo')->willReturn($fooResult);

        $barResult = $this->createMock(ValidateResult::class);
        $barResult->method('isValid')->willReturn(true);
        $barValidator = $this->createMock(Validator::class);
        $barValidator->expects(self::once())->method('validate')->with('bar')->willReturn($barResult);

        $validator = new PropertyValuesValidator(
            [
                'foo' => $fooValidator,
                'bar' => $barValidator,
            ],
        );

        $result = $validator->validate(
            [
                'foo' => 'foo',
                'bar' => 'bar',
                'fiz' => 'fiz',
            ],
        );

        self::assertTrue($result->isValid());
    }
}
