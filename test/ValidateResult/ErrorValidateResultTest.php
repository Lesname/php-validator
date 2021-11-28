<?php
declare(strict_types=1);

namespace LessValidatorTest\ValidateResult;

use LessValidator\ValidateResult\ErrorValidateResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\ValidateResult\ErrorValidateResult
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
