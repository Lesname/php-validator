<?php
declare(strict_types=1);

namespace LesValidatorTest\Builder;

use LesValidator\TypeValidator;
use LesValidator\ChainValidator;
use LesValidator\Number\BetweenValidator;
use LesValidator\Builder\NumericValidatorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\Builder\NumericValidatorBuilder
 */
class NumericValidatorBuilderTest extends TestCase
{
    public function testBasicBuild(): void
    {
        $validator = new NumericValidatorBuilder(false, 1, 3.5)
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
        $validator = new NumericValidatorBuilder(false, 5, 9.1)
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
