<?php
declare(strict_types=1);

namespace LessValidator;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use RuntimeException;

/**
 * @psalm-immutable
 */
final class TypeValidator implements Validator
{
    public const BOOLEAN = 'boolean';
    public const COLLECTION = 'collection';
    public const COMPOSITE = 'composite';
    public const INTEGER = 'integer';
    public const NUMBER = 'number';
    public const STRING = 'string';

    public function __construct(public string $type)
    {}

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

        return !$valid
            ? new ErrorValidateResult("type.expected.{$this->type}")
            : new ValidValidateResult();
    }

    /**
     * @psalm-suppress UnusedForeachValue for polyfill
     *
     * @todo php 8.1 use array_is_list
     *
     * @psalm-suppress MixedAssignment
     */
    private function isCollection(mixed $input): bool
    {
        if (!is_array($input)) {
            return false;
        }

        if (count($input) === 0) {
            return true;
        }

        $current_key = 0;
        foreach ($input as $key => $noop) {
            if ($key !== $current_key) {
                return false;
            }

            ++$current_key;
        }

        return true;
    }

    /**
     * @psalm-suppress UnusedForeachValue for polyfill
     *
     * @todo php 8.1 use array_is_list
     *
     * @psalm-suppress MixedAssignment
     */
    private function isComposite(mixed $input): bool
    {
        if (!is_array($input)) {
            return false;
        }

        if (count($input) === 0) {
            return true;
        }

        $current_key = 0;
        foreach ($input as $key => $noop) {
            if ($key !== $current_key) {
                return true;
            }

            ++$current_key;
        }

        return false;
    }
}
