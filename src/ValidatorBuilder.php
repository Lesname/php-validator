<?php
declare(strict_types=1);

namespace LessValidator;

use LessDocumentor\Type\Document\BoolTypeDocument;
use LessDocumentor\Type\Document\CollectionTypeDocument;
use LessDocumentor\Type\Document\CompositeTypeDocument;
use LessDocumentor\Type\Document\EnumTypeDocument;
use LessDocumentor\Type\Document\NumberTypeDocument;
use LessDocumentor\Type\Document\StringTypeDocument;
use LessDocumentor\Type\Document\TypeDocument;
use LessValidator\Collection\ItemsValidator;
use LessValidator\Collection\SizeValidator;
use LessValidator\Composite\PropertyKeysValidator;
use LessValidator\Composite\PropertyValuesValidator;
use LessValidator\Number\BetweenValidator;
use LessValidator\Number\PrecisionValidator;
use LessValidator\String\FormatValidator;
use LessValidator\String\LengthValidator;
use LessValidator\String\OptionsValidator;
use LessValueObject\String\Format\FormattedStringValueObject;
use RuntimeException;

final class ValidatorBuilder
{
    private ?TypeDocument $typeDocument = null;

    public function fromTypeDocument(TypeDocument $typeDocument): self
    {
        $this->typeDocument = $typeDocument;

        return $this;
    }

    public function build(): Validator
    {
        return match (true) {
            $this->typeDocument instanceof TypeDocument => $this->buildFromTypeDocument($this->typeDocument),
            default => throw new RuntimeException(),
        };
    }

    private function buildFromTypeDocument(TypeDocument $typeDocument): Validator
    {
        $validator = match (true) {
            $typeDocument instanceof BoolTypeDocument => TypeValidator::boolean(),
            $typeDocument instanceof CollectionTypeDocument => new ChainValidator(
                [
                    TypeValidator::collection(),
                    new SizeValidator($typeDocument->length->minimal, $typeDocument->length->maximal),
                    new ItemsValidator($this->buildFromTypeDocument($typeDocument->item)),
                ],
            ),
            $typeDocument instanceof CompositeTypeDocument => new ChainValidator(
                [
                    TypeValidator::composite(),
                    new PropertyKeysValidator(array_keys($typeDocument->properties)),
                    new PropertyValuesValidator(
                        array_map(
                            fn (TypeDocument $doc): Validator => $this->buildFromTypeDocument($doc),
                            $typeDocument->properties,
                        ),
                    ),
                ],
            ),
            $typeDocument instanceof EnumTypeDocument => new ChainValidator(
                [
                    TypeValidator::string(),
                    new OptionsValidator($typeDocument->cases)
                ],
            ),
            $typeDocument instanceof NumberTypeDocument => $this->buildFromNumberDocument($typeDocument),
            $typeDocument instanceof StringTypeDocument => $this->buildFromStringDocument($typeDocument),
            default => throw new RuntimeException(),
        };

        return !$typeDocument->isRequired()
            ? new NullableValidator($validator)
            : $validator;
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
