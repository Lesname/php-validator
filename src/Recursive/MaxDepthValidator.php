<?php
declare(strict_types=1);

namespace LesValidator\Recursive;

use Override;
use LesValidator\Validator;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ErrorValidateResult;

final class MaxDepthValidator implements Validator
{
    private int $depth = 0;

    public function __construct(
        private readonly Validator $subValidator,
        private readonly int $maxDepth,
    ) {
    }

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        if ($this->depth > $this->maxDepth) {
            return new ErrorValidateResult(
                'recursive.maxDepth',
                ['maxDepth' => $this->maxDepth],
            );
        }

        $this->depth += 1;

        $result = $this->subValidator->validate($input);

        $this->depth -= 1;

        return $result;
    }
}
