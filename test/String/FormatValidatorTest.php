<?php
declare(strict_types=1);

namespace LessValidatorTest\String;

use LessValidator\String\FormatValidator;
use LessValidator\ValidateResult\ErrorValidateResult;
use LessValueObject\String\Format\EmailAddress;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessValidator\String\FormatValidator
 */
final class FormatValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $validator = new FormatValidator(EmailAddress::class);

        self::assertTrue($validator->validate('a@b.c')->isValid());
    }

    public function testInvalid(): void
    {
        $validator = new FormatValidator(EmailAddress::class);

        $invalid = $validator->validate('a');

        self::assertFalse($invalid->isValid());
        self::assertInstanceOf(ErrorValidateResult::class, $invalid);
        self::assertSame('validation.string.format.emailAddress', $invalid->code);
    }
}
