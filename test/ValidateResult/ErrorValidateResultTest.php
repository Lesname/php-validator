<?php
declare(strict_types=1);

namespace LesValidatorTest\ValidateResult;

use LesValidator\ValidateResult\ErrorValidateResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\ValidateResult\ErrorValidateResult
 */
final class ErrorValidateResultTest extends TestCase
{
    public function testInvalid(): void
    {
        $result = new ErrorValidateResult('fiz');

        self::assertFalse($result->isValid());
    }

    public function testJson(): void
    {
        $result = new ErrorValidateResult('fiz', ['biz' => true]);

        self::assertSame(
            [
                'code' => 'fiz',
                'context' => ['biz' => true],
            ],
            $result->jsonSerialize(),
        );
    }
}
