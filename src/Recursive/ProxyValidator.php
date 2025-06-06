<?php
declare(strict_types=1);

namespace LesValidator\Recursive;

use Override;
use RuntimeException;
use LesValidator\Validator;
use LesValidator\ValidateResult\ValidateResult;

final class ProxyValidator implements Validator
{
    private ?Validator $proxy = null;

    public function setProxy(Validator $proxy): self
    {
        $this->proxy = $proxy;

        return $this;
    }

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        if ($this->proxy === null) {
            throw new RuntimeException();
        }

        return $this->proxy->validate($input);
    }
}
