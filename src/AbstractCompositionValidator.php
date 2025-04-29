<?php
declare(strict_types=1);

namespace LesValidator;

use Override;
use LesValidator\ValidateResult\ValidateResult;

/**
 * @psalm-immutable
 */
abstract class AbstractCompositionValidator implements Validator
{
    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        return $this
            ->composeValidator()
            ->validate($input);
    }

    abstract protected function composeValidator(): Validator;
}
