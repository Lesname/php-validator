<?php
declare(strict_types=1);

namespace LessValidator\Composite;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;
use RuntimeException;

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
        assert(is_array($input), new RuntimeException());

        $diff = array_diff(array_keys($input), $this->keys);

        if (count($diff) > 0) {
            return new ErrorValidateResult(
                'composite.keys.notAllowed',
                ['extra' => $diff],
            );
        }

        return new ValidValidateResult();
    }
}
