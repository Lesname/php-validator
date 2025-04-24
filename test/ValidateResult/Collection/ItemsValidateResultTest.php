<?php
declare(strict_types=1);

namespace LesValidatorTest\ValidateResult\Collection;

use LesValidator\ValidateResult\Collection\ItemsValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\ValidateResult\Collection\ItemsValidateResult
 */
final class ItemsValidateResultTest extends TestCase
{
    public function testValid(): void
    {
        $f = $this->createMock(ValidateResult::class);
        $f->method('isValid')->willReturn(true);

        $s = $this->createMock(ValidateResult::class);
        $s->method('isValid')->willReturn(true);

        $result = new ItemsValidateResult([$f, $s]);

        self::assertTrue($result->isValid());
    }

    public function testInvalid(): void
    {
        $f = $this->createMock(ValidateResult::class);
        $f->method('isValid')->willReturn(true);

        $s = $this->createMock(ValidateResult::class);
        $s->method('isValid')->willReturn(false);

        $result = new ItemsValidateResult([$f, $s]);

        self::assertFalse($result->isValid());
    }

    public function testJson(): void
    {
        $f = $this->createMock(ValidateResult::class);

        $s = $this->createMock(ValidateResult::class);

        $result = new ItemsValidateResult([$f, $s]);

        self::assertSame(
            [
                'items' => [
                    $f,
                    $s,
                ],
            ],
            $result->jsonSerialize(),
        );
    }
}
