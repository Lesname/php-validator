<?php
declare(strict_types=1);

namespace LesValidatorTest\Builder;

use LesValidator\TypeValidator;
use LesValidator\ChainValidator;
use LesValidator\String\LengthValidator;
use LesValidator\Composite\PropertyKeysValidator;
use LesValidator\Composite\PropertyValuesValidator;
use LesValidator\Builder\ArrayCompositeValidatorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesValidator\Builder\ArrayCompositeValidatorBuilder
 */
class ArrayCompositeValidatorBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $validator = new ArrayCompositeValidatorBuilder(['foo' => new LengthValidator(1, 2)])
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
