<?php
declare(strict_types=1);

namespace LesValidator\String;

use Override;
use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\Validator;
use LesValueObject\String\Format\StringFormatValueObject;

final class FormatValidator implements Validator
{
    /**
     * @param class-string<StringFormatValueObject> $format
     */
    public function __construct(public readonly string $format)
    {}

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_string($input));

        if (!$this->format::isFormat($input)) {
            $formatParts = explode('\\', $this->format);
            $name = lcfirst(array_pop($formatParts));

            return new ErrorValidateResult("string.format.{$name}");
        }

        return new ValidValidateResult();
    }
}
