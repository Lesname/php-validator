<?php
declare(strict_types=1);

namespace LesValidatorTest;

use LesValidator\NullableValidator;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\NullableValidator
 */
final class NullableValidatorTest extends TestCase
{
    public function testPassToSub(): void
    {
        $validateResult = $this->createMock(ValidateResult::class);

        $subValidator = $this->createMock(Validator::class);
        $subValidator
            ->expects(self::once())
            ->method('validate')
            ->with(1)
            ->willReturn($validateResult);

        $validator = new NullableValidator($subValidator);

        self::assertSame($validateResult, $validator->validate(1));
    }

    public function testNull(): void
    {
        $subValidator = $this->createMock(Validator::class);
        $subValidator
            ->expects(self::never())
            ->method('validate');

        $validator = new NullableValidator($subValidator);

        $validateResult = $validator->validate(null);

        self::assertTrue($validateResult->isValid());
    }
}
