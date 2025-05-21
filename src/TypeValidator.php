<?php
declare(strict_types=1);

namespace LesValidator;

use Override;
use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use RuntimeException;

/**
 * @psalm-immutable
 */
final class TypeValidator implements Validator
{
    public const string BOOLEAN = 'boolean';
    public const string COLLECTION = 'collection';
    public const string COMPOSITE = 'composite';
    public const string INTEGER = 'integer';
    public const string NUMBER = 'number';
    public const string STRING = 'string';

    /**
     * @psalm-pure
     */
    public function __construct(public readonly string $type)
    {}

    /**
     * @psalm-pure
     */
    public static function boolean(): self
    {
        return new self(self::BOOLEAN);
    }

    /**
     * @psalm-pure
     */
    public static function collection(): self
    {
        return new self(self::COLLECTION);
    }

    /**
     * @psalm-pure
     */
    public static function composite(): self
    {
        return new self(self::COMPOSITE);
    }

    /**
     * @psalm-pure
     */
    public static function integer(): self
    {
        return new self(self::INTEGER);
    }

    /**
     * @psalm-pure
     */
    public static function number(): self
    {
        return new self(self::NUMBER);
    }

    /**
     * @psalm-pure
     */
    public static function string(): self
    {
        return new self(self::STRING);
    }

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        $valid = match ($this->type) {
            self::BOOLEAN => is_bool($input),
            self::COLLECTION => $this->isCollection($input),
            self::COMPOSITE => $this->isComposite($input),
            self::INTEGER => is_int($input),
            self::NUMBER => is_float($input) || is_int($input),
            self::STRING => is_string($input),
            default => throw new RuntimeException(),
        };

        if ($valid === false) {
            if ($input === null) {
                return new ErrorValidateResult('required');
            }

            return new ErrorValidateResult("type.expected.{$this->type}");
        }

        return new ValidValidateResult();
    }

    private function isCollection(mixed $input): bool
    {
        return is_array($input) && array_is_list($input);
    }

    private function isComposite(mixed $input): bool
    {
        return is_array($input) && (count($input) === 0 || !array_is_list($input));
    }
}
