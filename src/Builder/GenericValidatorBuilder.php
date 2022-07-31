<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use BackedEnum;
use LessDocumentor\Type\Document\BoolTypeDocument;
use LessDocumentor\Type\Document\CollectionTypeDocument;
use LessDocumentor\Type\Document\CompositeTypeDocument;
use LessDocumentor\Type\Document\EnumTypeDocument;
use LessDocumentor\Type\Document\NumberTypeDocument;
use LessDocumentor\Type\Document\StringTypeDocument;
use LessDocumentor\Type\Document\TypeDocument;
use LessValidator\ChainValidator;
use LessValidator\Collection\ItemsValidator;
use LessValidator\Collection\SizeValidator;
use LessValidator\Composite\PropertyKeysValidator;
use LessValidator\Composite\PropertyValuesValidator;
use LessValidator\NullableValidator;
use LessValidator\Number\BetweenValidator;
use LessValidator\Number\PrecisionValidator;
use LessValidator\String\FormatValidator;
use LessValidator\String\LengthValidator;
use LessValidator\String\OptionsValidator;
use LessValidator\TypeValidator;
use LessValidator\Validator;
use LessValueObject\String\Format\FormattedStringValueObject;
use RuntimeException;

final class GenericValidatorBuilder implements TypeDocumentValidatorBuilder
{
    public function fromTypeDocument(TypeDocument $typeDocument): Validator
    {
        $validator = match (true) {
            $typeDocument instanceof BoolTypeDocument => TypeValidator::boolean(),
            $typeDocument instanceof CollectionTypeDocument => new ChainValidator(
                [
                    TypeValidator::collection(),
                    new SizeValidator($typeDocument->length->minimal, $typeDocument->length->maximal),
                    new ItemsValidator($this->fromTypeDocument($typeDocument->item)),
                ],
            ),
            $typeDocument instanceof CompositeTypeDocument => $this->buildFromCompositeDocument($typeDocument),
            $typeDocument instanceof EnumTypeDocument => new ChainValidator(
                [
                    TypeValidator::string(),
                    new OptionsValidator(
                        array_map(
                            static function (BackedEnum $item): string {
                                $value = $item->value;
                                assert(is_string($value));

                                return $value;
                            },
                            $typeDocument->cases,
                        )
                    ),
                ],
            ),
            $typeDocument instanceof NumberTypeDocument => $this->buildFromNumberDocument($typeDocument),
            $typeDocument instanceof StringTypeDocument => $this->buildFromStringDocument($typeDocument),
            default => throw new RuntimeException(),
        };

        return $typeDocument->isNullable()
            ? new NullableValidator($validator)
            : $validator;
    }

    private function buildFromCompositeDocument(CompositeTypeDocument $typeDocument): Validator
    {
        $validators = [TypeValidator::composite()];

        if ($typeDocument->allowAdditionalProperties === false) {
            $validators[] = new PropertyKeysValidator(array_keys($typeDocument->properties));
        }

        return new ChainValidator(
            [
                ...$validators,
                new PropertyValuesValidator(
                    array_map(
                        fn (TypeDocument $doc): Validator => $this->fromTypeDocument($doc),
                        $typeDocument->properties,
                    ),
                ),
            ],
        );
    }

    private function buildFromStringDocument(StringTypeDocument $typeDocument): Validator
    {
        $validators = [
            TypeValidator::string(),
            new LengthValidator($typeDocument->length->minimal, $typeDocument->length->maximal),
        ];

        $reference = $typeDocument->getReference();

        if ($reference && is_subclass_of($reference, FormattedStringValueObject::class)) {
            $validators[] = new FormatValidator($reference);
        }

        return new ChainValidator($validators);
    }

    private function buildFromNumberDocument(NumberTypeDocument $typeDocument): Validator
    {
        if ($typeDocument->precision->isSame(0)) {
            return new ChainValidator(
                [
                    TypeValidator::integer(),
                    new BetweenValidator($typeDocument->range->minimal, $typeDocument->range->maximal),
                ],
            );
        }

        return new ChainValidator(
            [
                TypeValidator::number(),
                new BetweenValidator($typeDocument->range->minimal, $typeDocument->range->maximal),
                new PrecisionValidator($typeDocument->precision->getValue()),
            ],
        );
    }
}
