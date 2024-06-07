<?php
declare(strict_types=1);

namespace LessValidatorTest\Builder;

use A\B;
use Throwable;
use LessValidator\TypeValidator;
use LessValidator\ChainValidator;
use LessValidator\String\LengthValidator;
use LessValidator\Builder\StringValidatorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Builder\StringValidatorBuilder
 */
class StringValidatorBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $validator = StringValidatorBuilder
            ::fromBetween(1, 5)
            ->build();

        self::assertEquals(
            new ChainValidator(
                [
                    TypeValidator::string(),
                    new LengthValidator(1, 5),
                ],
            ),
            $validator,
        );
    }

    public function testWithMin(): void
    {
        $nullBuilder = new StringValidatorBuilder();
        $oneBuilder = $nullBuilder->withMinLength(1);
        $twoBuilder = $oneBuilder->withMinLength(2);

        self::assertSame(null, $nullBuilder->getMinLength());
        self::assertSame(1, $oneBuilder->getMinLength());
        self::assertSame(2, $twoBuilder->getMinLength());
    }

    public function testWithMax(): void
    {
        $nullBuilder = new StringValidatorBuilder();
        $oneBuilder = $nullBuilder->withMaxLength(1);
        $twoBuilder = $oneBuilder->withMaxLength(2);

        self::assertSame(null, $nullBuilder->getMaxLength());
        self::assertSame(1, $oneBuilder->getMaxLength());
        self::assertSame(2, $twoBuilder->getMaxLength());
    }

    public function testMissingMinLength(): void
    {
        $this->expectException(Throwable::class);

        (new StringValidatorBuilder())->build();
    }

    public function testMissingMaLength(): void
    {
        $this->expectException(Throwable::class);

        (new StringValidatorBuilder())
            ->withMinLength(1)
            ->build();
    }
}
