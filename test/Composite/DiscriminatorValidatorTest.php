<?php

declare(strict_types=1);

namespace LesValidatorTest\Composite;

use stdClass;
use LesValidator\Validator;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\Composite\DiscriminatorValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DiscriminatorValidator::class)]
class DiscriminatorValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $property = new stdClass();
        $validResult = $this->createMock(ValidateResult::class);
        $validResult->expects(self::once())->method('isValid')->willReturn(true);

        $notMatchedValidator = $this->createMock(Validator::class);
        $notMatchedValidator->expects(self::never())->method('validate');

        $matchedValidator = $this->createMock(Validator::class);
        $matchedValidator
            ->expects(self::once())
            ->method('validate')
            ->with($property)
            ->willReturn($validResult);

        $validator = new DiscriminatorValidator(
            'foo',
            'bar',
            [
                'a' => $notMatchedValidator,
                'b' => $matchedValidator,
            ],
        );

        $result = $validator->validate(
            [
                'foo' => 'b',
                'bar' => $property,
            ]
        );

        self::assertTrue($result->isValid());
    }
}
