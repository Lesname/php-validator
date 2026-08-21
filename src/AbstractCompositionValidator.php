<?php

declare(strict_types=1);

namespace LesValidator;

use Override;
use LesValidator\ValidateResult\ValidateResult;

abstract class AbstractCompositionValidator implements Validator
{
    /**
     * @psalm-impure
     */
    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        return $this
            ->composeValidator()
            ->validate($input);
    }

    /**
     * @psalm-pure
     */
    abstract protected function composeValidator(): Validator;
}
