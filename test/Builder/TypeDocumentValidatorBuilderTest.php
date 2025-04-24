<?php
declare(strict_types=1);

namespace LesValidatorTest\Builder;

use LesDocumentor\Type\Document\BoolTypeDocument;
use LesDocumentor\Type\Document\Collection\Size;
use LesValidator\Builder\TypeDocumentValidatorBuilder;
use LesDocumentor\Type\Document\CollectionTypeDocument;
use LesDocumentor\Type\Document\Composite\Property;
use LesDocumentor\Type\Document\CompositeTypeDocument;
use LesDocumentor\Type\Document\EnumTypeDocument;
use LesDocumentor\Type\Document\Number\Range;
use LesDocumentor\Type\Document\NumberTypeDocument;
use LesDocumentor\Type\Document\String\Length;
use LesDocumentor\Type\Document\StringTypeDocument;
use LesDocumentor\Type\Document\TypeDocument;
use LesValueObject\Enum\ContentType;
use LesValueObject\String\Format\EmailAddress;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @covers \LesValidator\Builder\TypeDocumentValidatorBuilder
 */
final class TypeDocumentValidatorBuilderTest extends TestCase
{
    public function testFromBoolDocument(): void
    {
        $doc = new BoolTypeDocument();

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate(true)->isValid());
        self::assertTrue($validator->validate(false)->isValid());
        self::assertFalse($validator->validate(null)->isValid());
    }

    public function testFromCollectionDocument(): void
    {
        $doc = new CollectionTypeDocument(
            new BoolTypeDocument(null),
            new Size(1, 2),
            null,
        );

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate([true])->isValid());
        self::assertTrue($validator->validate([true, false])->isValid());
        self::assertFalse($validator->validate(null)->isValid());
        self::assertFalse($validator->validate([])->isValid());
        self::assertFalse($validator->validate([true, true, true])->isValid());
    }

    public function testFromCompositeDocument(): void
    {
        $doc = new CompositeTypeDocument(
            [
                'foo' => new Property(
                    new BoolTypeDocument(null),
                ),
            ],
        );

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate(['foo' => true])->isValid());
        self::assertFalse($validator->validate(null)->isValid());
        self::assertFalse($validator->validate([])->isValid());
        self::assertFalse($validator->validate(['foo' => false, 'bar' => false])->isValid());
        self::assertFalse($validator->validate(['foo' => 1])->isValid());
    }

    public function testFromCompositeDocumentAllowAdditionalProperties(): void
    {
        $doc = new CompositeTypeDocument(
            [
                'foo' => new Property(
                    new BoolTypeDocument(null),
                ),
            ],
            true,
        );

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate(['foo' => true])->isValid());
        self::assertTrue($validator->validate(['foo' => false, 'bar' => false])->isValid());

        self::assertFalse($validator->validate(null)->isValid());
        self::assertFalse($validator->validate([])->isValid());
        self::assertFalse($validator->validate(['foo' => 1])->isValid());
    }

    public function testFromEnumDocument(): void
    {
        $doc = new EnumTypeDocument(
            [
                ContentType::Text->value,
                ContentType::Markdown->value,
            ],
            null,
        );

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate('text')->isValid());
        self::assertTrue($validator->validate('markdown')->isValid());
        self::assertFalse($validator->validate(null)->isValid());
        self::assertFalse($validator->validate('bar')->isValid());
    }

    public function testFromStringDocument(): void
    {
        $doc = new StringTypeDocument(
            new Length(1, 3),
            null,
        );

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate('a')->isValid());
        self::assertTrue($validator->validate('ab')->isValid());
        self::assertTrue($validator->validate('abc')->isValid());
        self::assertFalse($validator->validate(null)->isValid());
        self::assertFalse($validator->validate('')->isValid());
        self::assertFalse($validator->validate('abcd')->isValid());
    }

    public function testFromStringDocumentFormatted(): void
    {
        $doc = new StringTypeDocument(
            new Length(1, 255),
            reference: EmailAddress::class,
        );

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate('a@b.c')->isValid());
        self::assertFalse($validator->validate(null)->isValid());
        self::assertFalse($validator->validate('abcd')->isValid());
    }

    public function testFromNumberDocumentFloat(): void
    {
        $doc = new NumberTypeDocument(
            new Range(-5, 5),
            .01,
        );
        $doc = $doc->withNullable();

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate(-4.12)->isValid());
        self::assertTrue($validator->validate(5)->isValid());
        self::assertTrue($validator->validate(null)->isValid());
        self::assertFalse($validator->validate(5.1)->isValid());
    }

    public function testFromNumberDocumentInt(): void
    {
        $doc = new NumberTypeDocument(
            new Range(1, 5),
            1,
        );

        $validator = (new TypeDocumentValidatorBuilder($doc))
            ->build();

        self::assertTrue($validator->validate(1)->isValid());
        self::assertTrue($validator->validate(5)->isValid());
        self::assertFalse($validator->validate(0)->isValid());
        self::assertFalse($validator->validate(6)->isValid());
        self::assertFalse($validator->validate(1.1)->isValid());
    }

    public function testFromUnknownDocument(): void
    {
        $this->expectException(Throwable::class);

        $doc = $this->createMock(TypeDocument::class);

        (new TypeDocumentValidatorBuilder($doc))
        ->build();
    }
}
