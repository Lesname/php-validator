<?php
declare(strict_types=1);

namespace LessValidatorTest\Builder;

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
}
