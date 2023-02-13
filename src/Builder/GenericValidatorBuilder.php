<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use LessDocumentor\Type\Document\BoolTypeDocument;
use LessDocumentor\Type\Document\CollectionTypeDocument;
use LessDocumentor\Type\Document\Composite\Property;
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
                    new SizeValidator($typeDocument->size->minimal, $typeDocument->size->maximal),
                    new ItemsValidator($this->fromTypeDocument($typeDocument->item)),
                ],
            ),
            $typeDocument instanceof CompositeTypeDocument => $this->buildFromCompositeDocument($typeDocument),
            $typeDocument instanceof EnumTypeDocument => new ChainValidator(
                [
                    TypeValidator::string(),
                    new OptionsValidator(
                        $typeDocument->cases,
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

        if ($typeDocument->allowExtraProperties === false) {
            $validators[] = new PropertyKeysValidator(array_keys($typeDocument->properties));
        }

        return new ChainValidator(
            [
                ...$validators,
                new PropertyValuesValidator(
                    array_map(
                        fn (Property $doc): Validator => $this->fromTypeDocument($doc->type),
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
        if ($typeDocument->precision === 0) {
            $validators = [TypeValidator::integer()];
        } else {
            $validators = [TypeValidator::number()];
        }

        if ($typeDocument->precision > 0) {
            $validators[] = new PrecisionValidator($typeDocument->precision);
        }

        if ($typeDocument->range->minimal !== null || $typeDocument->range->maximal !== null) {
            $validators[] = new BetweenValidator(
                $typeDocument->range->minimal,
                $typeDocument->range->maximal,
            );
        }

        return new ChainValidator($validators);
    }
}
