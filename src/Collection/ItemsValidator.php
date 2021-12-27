<?php
declare(strict_types=1);

namespace LessValidator\Collection;

use LessValidator\ValidateResult\Collection\ItemsValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\Validator;
use RuntimeException;

/**
 * @psalm-immutable
 */
final class ItemsValidator implements Validator
{
    public function __construct(public Validator $itemValidator)
    {}

    /**
     * @psalm-suppress MixedAssignment
     */
    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input), new RuntimeException());

        return new ItemsValidateResult(
            (function (mixed $input): iterable {
                foreach ($input as $value) {
                    yield $this->itemValidator->validate($value);
                }
            })($input),
        );
    }
}
