<?php

declare(strict_types=1);

namespace LesValidator\Collection;

use Override;
use LesValidator\ValidateResult\Collection\ItemsValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\Validator;

final class ItemsValidator implements Validator
{
    /**
     * @psalm-mutation-free
     */
    public function __construct(public readonly Validator $itemValidator)
    {}

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input) && array_is_list($input));

        $itemValidator = $this->itemValidator;

        return new ItemsValidateResult(
            array_map(
                static fn (mixed $item): ValidateResult => $itemValidator->validate($item),
                $input,
            ),
        );
    }
}
