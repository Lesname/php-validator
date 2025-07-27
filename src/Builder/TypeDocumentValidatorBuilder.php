<?php
declare(strict_types=1);

namespace LesValidator\Builder;

use Closure;
use Override;
use ReflectionClass;
use RuntimeException;
use LesValidator\Validator;
use LesValidator\AnyValidator;
use LesValidator\TypeValidator;
use LesValidator\ChainValidator;
use LesValidator\NullableValidator;
use LesValidator\String\LengthValidator;
use LesValidator\String\FormatValidator;
use LesValidator\String\OptionsValidator;
use LesValidator\Number\BetweenValidator;
use LesValidator\Collection\SizeValidator;
use LesValidator\Recursive\ProxyValidator;
use LesValidator\Collection\ItemsValidator;
use LesValidator\Number\MultipleOfValidator;
use LesDocumentor\Type\Document\TypeDocument;
use LesValidator\Composite\PropertyValidator;
use LesValidator\Recursive\MaxDepthValidator;
use LesDocumentor\Type\Document\BoolTypeDocument;
use LesDocumentor\Type\Document\EnumTypeDocument;
use LesValidator\Composite\PropertyKeysValidator;
use LesDocumentor\Type\Document\NullTypeDocument;
use LesDocumentor\Type\Document\UnionTypeDocument;
use LesDocumentor\Type\Document\NumberTypeDocument;
use LesDocumentor\Type\Document\StringTypeDocument;
use LesValidator\Composite\PropertyValuesValidator;
use LesDocumentor\Type\Document\NestedTypeDocument;
use LesDocumentor\Type\Document\CompositeTypeDocument;
use LesDocumentor\Type\Document\CollectionTypeDocument;
use LesValidator\Builder\Attribute\AdditionalValidator;
use LesDocumentor\Type\Document\Composite\Key\ExactKey;
use LesValueObject\String\Format\StringFormatValueObject;

final class TypeDocumentValidatorBuilder implements ValidatorBuilder
{
    /** @var array<string, array{validator: Validator, used: boolean}> */
    private static array $recursiveValidators = [];

    public function __construct(private readonly ?TypeDocument $typeDocument = null)
    {}

    public function withTypeDocument(TypeDocument $typeDocument): self
    {
        return new self($typeDocument);
    }

    /**
     * @psalm-suppress ImpureMethodCall
     * @psalm-suppress ImpureFunctionCall
     * @psalm-suppress DeprecatedMethod
     */
    #[Override]
    public function build(): Validator
    {
        if ($this->typeDocument === null) {
            throw new RuntimeException();
        }

        $typeDocument = $this->typeDocument;

        $validator = match (true) {
            $typeDocument instanceof BoolTypeDocument => TypeValidator::boolean(),
            $typeDocument instanceof CollectionTypeDocument => $this->buildForRecursiveTypeDocument($typeDocument, $this->buildFromCollectionTypeDocument(...)),
            $typeDocument instanceof CompositeTypeDocument => $this->buildForRecursiveTypeDocument($typeDocument, $this->buildFromCompositeTypeDocument(...)),
            $typeDocument instanceof EnumTypeDocument => new ChainValidator(
                [
                    TypeValidator::string(),
                    new OptionsValidator(
                        $typeDocument->cases,
                    ),
                ],
            ),
            $typeDocument instanceof NumberTypeDocument => $this->buildFromNumberTypeDocument($typeDocument),
            $typeDocument instanceof StringTypeDocument => $this->buildFromStringTypeDocument($typeDocument),
            $typeDocument instanceof UnionTypeDocument => $this->buildFromUnionTypeDocument($typeDocument),
            default => throw new RuntimeException(
                sprintf(
                    'Type "%s" is not supported',
                    $typeDocument::class,
                ),
            ),
        };

        $additionalValidators = $this->getAdditionalValidators($typeDocument);

        if (count($additionalValidators) > 0) {
            $validator = new ChainValidator([$validator, ...$additionalValidators]);
        }

        /** @phpstan-ignore method.deprecated */
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

    /**
     * @param Closure(TypeDocument): Validator $builder
     */
    private function buildForRecursiveTypeDocument(TypeDocument $typeDocument, Closure $builder): Validator
    {
        if ($typeDocument->getReference() === null) {
            return $builder($typeDocument);
        }

        $key = $typeDocument->getReference();

        if (isset(self::$recursiveValidators[$key])) {
            self::$recursiveValidators[$key]['used'] = true;

            return self::$recursiveValidators[$key]['validator'];
        }

        $proxy = new ProxyValidator();

        self::$recursiveValidators[$key] = [
            'used' => false,
            'validator' => $proxy,
        ];

        $validator = $builder($typeDocument);

        /** @psalm-suppress TypeDoesNotContainType */
        if (self::$recursiveValidators[$key]['used']) {
            $maxDepth = 16;

            if ($typeDocument instanceof NestedTypeDocument) {
                $maxDepth = $typeDocument->getMaxDepth() ?? $maxDepth;
            }

            $validator = $proxy->setProxy(
                new MaxDepthValidator(
                    $validator,
                    $maxDepth,
                ),
            );
        }

        unset(self::$recursiveValidators[$key]);

        return $validator;
    }

    private function buildFromCollectionTypeDocument(TypeDocument $typeDocument): Validator
    {
        if (!$typeDocument instanceof CollectionTypeDocument) {
            throw new RuntimeException();
        }

        $chained = [TypeValidator::collection()];

        if ($typeDocument->size) {
            $chained[] = new SizeValidator($typeDocument->size->minimal, $typeDocument->size->maximal);
        }

        $chained[] = new ItemsValidator($this->withTypeDocument($typeDocument->item)->build());

        return new ChainValidator($chained);
    }

    /**
     * @psalm-suppress ImpureMethodCall
     */
    private function buildFromCompositeTypeDocument(TypeDocument $typeDocument): Validator
    {
        if (!$typeDocument instanceof CompositeTypeDocument) {
            throw new RuntimeException();
        }

        $validators = [TypeValidator::composite()];

        $propertyValidators = [];
        $keys = [];

        foreach ($typeDocument->properties as $property) {
            $propertyValidator = $this->withTypeDocument($property->type)->build();
            $keys[] = $property->key;

            if ($property->default !== null && !$propertyValidator instanceof NullableValidator) {
                $propertyValidator = new NullableValidator($propertyValidator);
            }

            if ($property->key instanceof ExactKey) {
                $propertyValidators[$property->key->value] = $propertyValidator;
            } else {
                $validators[] = new PropertyValidator($property->key, $propertyValidator);
            }
        }

        if ($typeDocument->allowExtraProperties === false) {
            $validators[] = new PropertyKeysValidator($keys);
        }

        return new ChainValidator(
            [
                ...$validators,
                new PropertyValuesValidator($propertyValidators),
            ],
        );
    }

    private function buildFromStringTypeDocument(StringTypeDocument $typeDocument): Validator
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

    private function buildFromNumberTypeDocument(NumberTypeDocument $typeDocument): Validator
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

    private function buildFromUnionTypeDocument(UnionTypeDocument $typeDocument): Validator
    {
        $subValidators = [];
        /** @phpstan-ignore method.deprecated */
        $nullable = $typeDocument->isNullable();

        foreach ($typeDocument->subTypes as $subType) {
            if ($subType instanceof NullTypeDocument) {
                $nullable = true;
            } else {
                $subValidators[] = $this->withTypeDocument($subType)->build();
            }
        }

        $validator = count($subValidators) !== 1
            ? new AnyValidator($subValidators)
            : array_pop($subValidators);

        if ($nullable) {
            return new NullableValidator($validator);
        }

        return $validator;
    }
}
