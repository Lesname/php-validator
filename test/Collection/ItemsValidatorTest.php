<?php
declare(strict_types=1);

namespace LessValidatorTest\Collection;

use LessValidator\Collection\ItemsValidator;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Collection\ItemsValidator
 */
final class ItemsValidatorTest extends TestCase
{
    public function testValidItems(): void
    {
        $fResult = $this->createMock(ValidateResult::class);
        $fResult->expects(self::once())->method('isValid')->willReturn(true);

        $sResult = $this->createMock(ValidateResult::class);
        $sResult->expects(self::once())->method('isValid')->willReturn(true);

        $itemValidator = $this->createMock(Validator::class);
        $itemValidator
            ->expects(self::exactly(2))
            ->method('validate')
            ->willReturnOnConsecutiveCalls($fResult, $sResult);

        $validator = new ItemsValidator($itemValidator);

        self::assertTrue($validator->validate(['fiz', 'biz'])->isValid());
    }
}
