<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use ReflectionClass;
use ReflectionAttribute;
use LessValidator\Number\MultipleOfValidator;
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
use LessValidator\String\FormatValidator;
use LessValidator\String\LengthValidator;
use LessValidator\String\OptionsValidator;
use LessValidator\TypeValidator;
use LessValidator\Validator;
use RuntimeException;
use LessValidator\Builder\Attribute\AdditionalValidator;
use LessValueObject\String\Format\StringFormatValueObject;

final class GenericValidatorBuilder implements TypeDocumentValidatorBuilder
{
    public function fromTypeDocument(TypeDocument $typeDocument): Validator
    {
        $validator = match (true) {
            $typeDocument instanceof BoolTypeDocument => TypeValidator::boolean(),
            $typeDocument instanceof CollectionTypeDocument => $this->buildFromCollectionDocument($typeDocument),
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

        $reference = $typeDocument->getReference();

        if ($reference && class_exists($reference)) {
            $classReflection = new ReflectionClass($reference);

            $additionalValidators = array_map(
                static function (ReflectionAttribute $attribute) {
                    $className = $attribute->newInstance();

                    return new $className->validator();
                },
                $classReflection->getAttributes(AdditionalValidator::class),
            );

            if (count($additionalValidators) > 0) {
                $validator = new ChainValidator(
                    [
                        $validator,
                        ...$additionalValidators,
                    ],
                );
            }
        }

        return $typeDocument->isNullable()
            ? new NullableValidator($validator)
            : $validator;
    }

    private function buildFromCollectionDocument(CollectionTypeDocument $typeDocument): Validator
    {
        $chained = [TypeValidator::collection()];

        if ($typeDocument->size) {
            $chained[] = new SizeValidator($typeDocument->size->minimal, $typeDocument->size->maximal);
        }

        $chained[] = new ItemsValidator($this->fromTypeDocument($typeDocument->item));

        return new ChainValidator($chained);
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
        $validators = [TypeValidator::string()];

        if ($typeDocument->length) {
            $validators[] = new LengthValidator($typeDocument->length->minimal, $typeDocument->length->maximal);
        }

        $reference = $typeDocument->getReference();

        if ($reference && is_subclass_of($reference, StringFormatValueObject::class)) {
            $validators[] = new FormatValidator($reference);
        }

        return new ChainValidator($validators);
    }

    private function buildFromNumberDocument(NumberTypeDocument $typeDocument): Validator
    {
        if (is_int($typeDocument->multipleOf)) {
            $validators = [TypeValidator::integer()];
        } else {
            $validators = [TypeValidator::number()];
        }

        if ($typeDocument->range !== null) {
            $validators[] = new BetweenValidator(
                $typeDocument->range->minimal,
                $typeDocument->range->maximal,
            );
        }

        if ($typeDocument->multipleOf !== null) {
            $validators[] = new MultipleOfValidator($typeDocument->multipleOf);
        }

        return new ChainValidator($validators);
    }
}
