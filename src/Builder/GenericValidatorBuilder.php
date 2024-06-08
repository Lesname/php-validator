<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use ReflectionClass;
use LessValidator\Number\MultipleOfValidator;
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
use LessValidator\String\FormatValidator;
use LessValidator\String\LengthValidator;
use LessValidator\String\OptionsValidator;
use LessValidator\TypeValidator;
use LessValidator\Validator;
use RuntimeException;
use LessValidator\Builder\Attribute\AdditionalValidator;
use LessValueObject\String\Format\StringFormatValueObject;

/**
 * @psalm-immutable
 */
final class GenericValidatorBuilder implements ValidatorBuilder
{
    private ?TypeDocument $typeDocument = null;

    public function withTypeDocument(TypeDocument $typeDocument): self
    {
        $clone = clone $this;
        $clone->typeDocument = $typeDocument;

        return $clone;
    }

    /**
     * @psalm-suppress ImpureMethodCall
     * @psalm-suppress ImpureFunctionCall
     */
    public function build(): Validator
    {
        if ($this->typeDocument === null) {
            throw new RuntimeException();
        }

        $typeDocument = $this->typeDocument;

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

        if (is_string($reference) && class_exists($reference)) {
            $classReflection = new ReflectionClass($reference);

            $additionalValidators = [];

            foreach ($classReflection->getAttributes(AdditionalValidator::class) as $attribute) {
                $className = $attribute->newInstance();

                $additionalValidators[] = new $className->validator();
            }

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

    /**
     * @deprecated use withTypeDocument
     */
    public function fromTypeDocument(TypeDocument $typeDocument): Validator
    {
        return $this->withTypeDocument($typeDocument)->build();
    }

    private function buildFromCollectionDocument(CollectionTypeDocument $typeDocument): Validator
    {
        $chained = [TypeValidator::collection()];

        if ($typeDocument->size) {
            $chained[] = new SizeValidator($typeDocument->size->minimal, $typeDocument->size->maximal);
        }

        $chained[] = new ItemsValidator($this->withTypeDocument($typeDocument->item)->build());

        return new ChainValidator($chained);
    }

    private function buildFromCompositeDocument(CompositeTypeDocument $typeDocument): Validator
    {
        $validators = [TypeValidator::composite()];

        if ($typeDocument->allowExtraProperties === false) {
            $validators[] = new PropertyKeysValidator(array_keys($typeDocument->properties));
        }

        $propertyValidators = [];

        foreach ($typeDocument->properties as $key => $property) {
            $propertyValidators[$key] = $this->withTypeDocument($property->type)->build();
        }

        return new ChainValidator(
            [
                ...$validators,
                new PropertyValuesValidator($propertyValidators),
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

        if (is_string($reference) && is_subclass_of($reference, StringFormatValueObject::class)) {
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
