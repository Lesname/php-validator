<?php
declare(strict_types=1);

namespace LesValidatorTest\Recursive;

use LesValidator\Validator;
use LesValidator\Recursive\MaxDepthValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ErrorValidateResult;

#[CoversClass(MaxDepthValidator::class)]
class MaxDepthValidatorTest extends TestCase
{
    public function testCallsSubValidator(): void
    {
        $input = [];

        $result = $this->createMock(ValidateResult::class);

        $subValidator = $this->createMock(Validator::class);
        $subValidator
            ->expects($this->once())
            ->method('validate')
            ->with($input)
            ->willReturn($result);

        $validator = new MaxDepthValidator($subValidator, 1);

        self::assertSame($result, $validator->validate($input));
    }

    public function testMaxDepth(): void
    {
        $input = [];

        $result = $this->createMock(ValidateResult::class);

        $subValidator = $this->createMock(Validator::class);

        $validator = new MaxDepthValidator($subValidator, 0);

        $subValidator
            ->expects($this->once())
            ->method('validate')
            ->with($input)
            ->willReturnCallback(
                function ($input) use ($validator) {
                    return $validator->validate($input);
                }
            );

        $result = $validator->validate($input);
        self::assertFalse($result->isValid());
        self::assertInstanceOf(ErrorValidateResult::class, $result);
        self::assertSame('recursive.maxDepth', $result->code);
    }
}
