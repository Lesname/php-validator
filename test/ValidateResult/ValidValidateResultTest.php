<?php
declare(strict_types=1);

namespace LesValidatorTest\ValidateResult;

use LesValidator\ValidateResult\ValidValidateResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\ValidateResult\ValidValidateResult
 */
final class ValidValidateResultTest extends TestCase
{
    public function testSetup(): void
    {
        $result = new ValidValidateResult();

        self::assertTrue($result->isValid());
        self::assertNull($result->jsonSerialize());
    }
}
