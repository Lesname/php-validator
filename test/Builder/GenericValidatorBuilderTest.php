<?php
declare(strict_types=1);

namespace LessValidatorTest\Builder;

use LessDocumentor\Type\Document\BoolTypeDocument;
use LessDocumentor\Type\Document\Collection\Size;
use LessDocumentor\Type\Document\CollectionTypeDocument;
use LessDocumentor\Type\Document\Composite\Property;
use LessDocumentor\Type\Document\CompositeTypeDocument;
use LessDocumentor\Type\Document\EnumTypeDocument;
use LessDocumentor\Type\Document\Number\Range;
use LessDocumentor\Type\Document\NumberTypeDocument;
use LessDocumentor\Type\Document\String\Length;
use LessDocumentor\Type\Document\StringTypeDocument;
use LessDocumentor\Type\Document\TypeDocument;
use LessValidator\Builder\GenericValidatorBuilder;
use LessValueObject\Enum\ContentType;
use LessValueObject\Number\Int\Unsigned;
use LessValueObject\String\Format\EmailAddress;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @covers \LessValidator\Builder\GenericValidatorBuilder
 */
final class GenericValidatorBuilderTest extends TestCase
{
    public function testFromBoolDocument(): void
    {
        $doc = new BoolTypeDocument();


        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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
                ContentType::Text->getValue(),
                ContentType::Markdown->getValue(),
            ],
            null,
        );

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

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

        $validator = (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);

        self::assertTrue($validator->validate(1)->isValid());
        self::assertTrue($validator->validate(5)->isValid());
        self::assertFalse($validator->validate(0)->isValid());
        self::assertFalse($validator->validate(6)->isValid());
        self::assertFalse($validator->validate(1.1)->isValid());
    }

    public function testFromUnkownDocument(): void
    {
        $this->expectException(Throwable::class);

        $doc = $this->createMock(TypeDocument::class);

        (new GenericValidatorBuilder())
            ->fromTypeDocument($doc);
    }
}
