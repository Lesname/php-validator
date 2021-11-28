<?php
declare(strict_types=1);

namespace LessValidatorTest\ValidateResult\Collection;

use LessValidator\ValidateResult\Collection\SelfValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\ValidateResult\Collection\SelfValidateResult
 */
final class SelfValidateResultTest extends TestCase
{
    public function testProxyValid(): void
    {
        $sub = $this->createMock(ValidateResult::class);
        $sub
            ->expects(self::exactly(2))
            ->method('isValid')
            ->willReturnOnConsecutiveCalls(true, false);

        $result = new SelfValidateResult($sub);

        self::assertTrue($result->isValid());
        self::assertFalse($result->isValid());
    }

    public function testJson(): void
    {
        $sub = $this->createMock(ValidateResult::class);

        $result = new SelfValidateResult($sub);

        self::assertSame(['self' => $sub], $result->jsonSerialize());
    }
}
