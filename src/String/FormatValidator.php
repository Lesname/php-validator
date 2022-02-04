<?php
declare(strict_types=1);

namespace LessValidator\String;

use LessValidator\Exception\UnexpectedType;
use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;
use LessValueObject\String\Format\FormattedStringValueObject;
use ReflectionClass;
use ReflectionException;

/**
 * @psalm-immutable
 */
final class FormatValidator implements Validator
{
    /**
     * @param class-string<FormattedStringValueObject> $format
     */
    public function __construct(public readonly string $format)
    {}

    /**
     * @throws ReflectionException
     *
     * @psalm-suppress ImpureMethodCall getShortName
     */
    public function validate(mixed $input): ValidateResult
    {
        assert(is_string($input), new UnexpectedType('string', get_debug_type($input)));

        if (!$this->format::isFormat($input)) {
            $name = lcfirst((new ReflectionClass($this->format))->getShortName());

            return new ErrorValidateResult("string.format.{$name}");
        }

        return new ValidValidateResult();
    }
}
