<?php
declare(strict_types=1);

namespace LessValidatorTest\Builder;

use LessValidator\TypeValidator;
use LessValidator\ChainValidator;
use LessValidator\Number\BetweenValidator;
use LessValidator\Builder\NumericValidatorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Builder\NumericValidatorBuilder
 */
class NumericValidatorBuilderTest extends TestCase
{
    public function testBasicBuild(): void
    {
        $validator = NumericValidatorBuilder
            ::fromBetween(1, 3.5)
            ->build();

        self::assertEquals(
            new ChainValidator(
                [
                    TypeValidator::number(),
                    new BetweenValidator(1, 3.5),
                ],
            ),
            $validator,
        );
    }

    public function testOnlyIntegers(): void
    {
        $validator = NumericValidatorBuilder
            ::fromBetween(5, 9.1)
            ->withOnlyIntegers()
            ->build();

        self::assertEquals(
            new ChainValidator(
                [
                    TypeValidator::integer(),
                    new BetweenValidator(5, 9.1),
                ],
            ),
            $validator,
        );
    }
}
