<?php
declare(strict_types=1);

namespace LessValidatorTest\Builder;

use LessValidator\TypeValidator;
use LessValidator\ChainValidator;
use LessValidator\String\LengthValidator;
use LessValidator\Composite\PropertyKeysValidator;
use LessValidator\Composite\PropertyValuesValidator;
use LessValidator\Builder\ArrayCompositeValidatorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\Builder\ArrayCompositeValidatorBuilder
 */
class ArrayCompositeValidatorBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $validator = ArrayCompositeValidatorBuilder
            ::fromArrayCompositeValidators(['foo' => new LengthValidator(1, 2)])
            ->build();

        self::assertEquals(
            new ChainValidator(
                [
                    TypeValidator::composite(),
                    new PropertyKeysValidator(['foo']),
                    new PropertyValuesValidator(['foo' => new LengthValidator(1, 2)])
                ],
            ),
            $validator
        );
    }
}
