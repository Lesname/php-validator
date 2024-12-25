<?php
declare(strict_types=1);

namespace LessValidator\String;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;
use LessValueObject\String\Format\StringFormatValueObject;
use ReflectionClass;
use ReflectionException;

/**
 * @psalm-immutable
 */
final class FormatValidator implements Validator
{
    /**
     * @param class-string<StringFormatValueObject> $format
     */
    public function __construct(public readonly string $format)
    {}

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
