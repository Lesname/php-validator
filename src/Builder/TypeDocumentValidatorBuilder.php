<?php
declare(strict_types=1);

namespace LesValidator\Builder;

use ReflectionClass;
use RuntimeException;
use LesValidator\Validator;
use LesValidator\TypeValidator;
use LesValidator\ChainValidator;
use LesValidator\NullableValidator;
use LesValidator\String\LengthValidator;
use LesValidator\String\FormatValidator;
use LesValidator\String\OptionsValidator;
use LesValidator\Number\BetweenValidator;
use LesValidator\Collection\SizeValidator;
use LesValidator\Collection\ItemsValidator;
use LesValidator\Number\MultipleOfValidator;
use LesDocumentor\Type\Document\TypeDocument;
use LesDocumentor\Type\Document\BoolTypeDocument;
use LesDocumentor\Type\Document\EnumTypeDocument;
use LesValidator\Composite\PropertyKeysValidator;
use LesDocumentor\Type\Document\NumberTypeDocument;
use LesDocumentor\Type\Document\StringTypeDocument;
use LesValidator\Composite\PropertyValuesValidator;
use LesDocumentor\Type\Document\CompositeTypeDocument;
use LesDocumentor\Type\Document\CollectionTypeDocument;
use LesValidator\Builder\Attribute\AdditionalValidator;
use LesValueObject\String\Format\StringFormatValueObject;

/**
 * @psalm-immutable
 */
final class TypeDocumentValidatorBuilder implements ValidatorBuilder
{
    public function __construct(private readonly ?TypeDocument $typeDocument = null)
    {}

    public function withTypeDocument(TypeDocument $typeDocument): self
    {
        return new self($typeDocument);
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

        $additionalValidators = $this->getAdditionalValidators($typeDocument);

        if (count($additionalValidators) > 0) {
            $validator = new ChainValidator([$validator, ...$additionalValidators]);
        }

        return $typeDocument->isNullable()
            ? new NullableValidator($validator)
            : $validator;
    }

    /**
     * @psalm-suppress ImpureMethodCall newInstance
     * @psalm-suppress ImpureFunctionCall class_exists
     *
     * @return array<Validator>
     */
    private function getAdditionalValidators(TypeDocument $typeDocument): array
    {
        $reference = $typeDocument->getReference();
        $additionalValidators = [];

        if (is_string($reference) && class_exists($reference)) {
            $classReflection = new ReflectionClass($reference);

            foreach ($classReflection->getAttributes(AdditionalValidator::class) as $attribute) {
                $className = $attribute->newInstance();

                $additionalValidators[] = new $className->validator();
            }
        }

        return $additionalValidators;
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
