<?php
declare(strict_types=1);

namespace LesValidatorTest\ValidateResult\Composite;

use LesValidator\ValidateResult\Composite\PropertiesValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\ValidateResult\Composite\PropertiesValidateResult
 */
final class PropertiesValidateResultTest extends TestCase
{
    public function testValid(): void
    {
        $foo = $this->createMock(ValidateResult::class);
        $foo->expects(self::once())->method('isValid')->willReturn(true);

        $bar = $this->createMock(ValidateResult::class);
        $bar->expects(self::once())->method('isValid')->willReturn(true);

        $result = new PropertiesValidateResult(
            [
                'foo' => $foo,
                'bar' => $bar,
            ],
        );

        self::assertTrue($result->isValid());
    }

    public function testInvalid(): void
    {
        $foo = $this->createMock(ValidateResult::class);
        $foo->expects(self::once())->method('isValid')->willReturn(true);

        $bar = $this->createMock(ValidateResult::class);
        $bar->expects(self::once())->method('isValid')->willReturn(false);

        $result = new PropertiesValidateResult(
            [
                'foo' => $foo,
                'bar' => $bar,
            ],
        );

        self::assertFalse($result->isValid());
    }

    public function testProperties(): void
    {
        $foo = $this->createMock(ValidateResult::class);

        $bar = $this->createMock(ValidateResult::class);

        $result = new PropertiesValidateResult(
            [
                'foo' => $foo,
                'bar' => $bar,
            ],
        );

        self::assertSame(
            [
                'properties' => [
                    'foo' => $foo,
                    'bar' => $bar,
                ],
            ],
            $result->jsonSerialize(),
        );
    }
}
