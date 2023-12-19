<?php
declare(strict_types=1);

namespace LessValidator\Composite;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;
use LessValidator\ValidateResult\Composite\SelfValidateResult;

/**
 * @psalm-immutable
 */
final class PropertyKeysValidator implements Validator
{
    /** @var array<string> */
    public array $keys = [];

    /** @param iterable<int, string> $keys */
    public function __construct(iterable $keys)
    {
        foreach ($keys as $key) {
            $this->keys[] = $key;
        }
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
