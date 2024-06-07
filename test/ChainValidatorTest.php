<?php
declare(strict_types=1);

namespace LessValidatorTest;

use LessValidator\ChainValidator;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\ChainValidator
 */
final class ChainValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $fResult = $this->createMock(ValidateResult::class);
        $fResult->expects(self::once())->method('isValid')->willReturn(true);

        $f = $this->createMock(Validator::class);
        $f->expects(self::once())->method('validate')->with('foo')->willReturn($fResult);

        $sResult = $this->createMock(ValidateResult::class);
        $sResult->expects(self::once())->method('isValid')->willReturn(true);

        $s = $this->createMock(Validator::class);
        $s->expects(self::once())->method('validate')->with('foo')->willReturn($sResult);

        $validator = ChainValidator::chain($f, $s);

        self::assertTrue($validator->validate('foo')->isValid());
    }

    public function testInvalid(): void
    {
        $fResult = $this->createMock(ValidateResult::class);
        $fResult->expects(self::once())->method('isValid')->willReturn(true);

        $f = $this->createMock(Validator::class);
        $f->expects(self::once())->method('validate')->with('foo')->willReturn($fResult);

        $sResult = $this->createMock(ValidateResult::class);
        $sResult->expects(self::once())->method('isValid')->willReturn(false);

        $s = $this->createMock(Validator::class);
        $s->expects(self::once())->method('validate')->with('foo')->willReturn($sResult);

        $validator = new ChainValidator([$f, $s]);

        self::assertSame($sResult, $validator->validate('foo'));
    }
}
