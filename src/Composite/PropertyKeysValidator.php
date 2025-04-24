<?php
declare(strict_types=1);

namespace LesValidator\Composite;

use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\Validator;
use LesValidator\ValidateResult\Composite\SelfValidateResult;

/**
 * @psalm-immutable
 */
final class PropertyKeysValidator implements Validator
{
    /** @var array<string> */
    public readonly array $keys;

    /** @param iterable<int, string> $keys */
    public function __construct(iterable $keys)
    {
        $keysArray = [];

        foreach ($keys as $key) {
            $keysArray[] = $key;
        }

        $this->keys = $keysArray;
    }

    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input));

        $diff = array_diff(array_keys($input), $this->keys);

        if (count($diff) > 0) {
            return new SelfValidateResult(
                new ErrorValidateResult(
                    'composite.keysNotAllowed',
                    ['extra' => array_values($diff)],
                ),
            );
        }

        return new ValidValidateResult();
    }
}
