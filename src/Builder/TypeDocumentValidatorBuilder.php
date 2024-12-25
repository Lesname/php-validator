<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use ReflectionClass;
use RuntimeException;
use LessValidator\Validator;
use LessValidator\TypeValidator;
use LessValidator\ChainValidator;
use LessValidator\NullableValidator;
use LessValidator\String\LengthValidator;
use LessValidator\String\FormatValidator;
use LessValidator\String\OptionsValidator;
use LessValidator\Number\BetweenValidator;
use LessValidator\Collection\SizeValidator;
use LessValidator\Collection\ItemsValidator;
use LessValidator\Number\MultipleOfValidator;
use LessDocumentor\Type\Document\TypeDocument;
use LessDocumentor\Type\Document\BoolTypeDocument;
use LessDocumentor\Type\Document\EnumTypeDocument;
use LessValidator\Composite\PropertyKeysValidator;
use LessDocumentor\Type\Document\NumberTypeDocument;
use LessDocumentor\Type\Document\StringTypeDocument;
use LessValidator\Composite\PropertyValuesValidator;
use LessDocumentor\Type\Document\CompositeTypeDocument;
use LessDocumentor\Type\Document\CollectionTypeDocument;
use LessValidator\Builder\Attribute\AdditionalValidator;
use LessValueObject\String\Format\StringFormatValueObject;

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
