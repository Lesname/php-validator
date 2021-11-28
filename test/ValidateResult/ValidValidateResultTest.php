<?php
declare(strict_types=1);

namespace LessValidatorTest\ValidateResult;

use LessValidator\ValidateResult\ValidValidateResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\ValidateResult\ValidValidateResult
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
