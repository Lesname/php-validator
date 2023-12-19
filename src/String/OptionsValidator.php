<?php
declare(strict_types=1);

namespace LessValidator\String;

use LessValidator\ValidateResult\ErrorValidateResult;
use LessValidator\ValidateResult\ValidateResult;
use LessValidator\ValidateResult\ValidValidateResult;
use LessValidator\Validator;

/**
 * @psalm-immutable
 */
final class OptionsValidator implements Validator
{
    /** @var array<string> */
    public array $options = [];

    /** @param iterable<string> $options */
    public function __construct(iterable $options)
    {
        foreach ($options as $option) {
            $this->options[] = $option;
        }
    }

    public function validate(mixed $input): ValidateResult
    {
        if (in_array($input, $this->options, true)) {
            return new ValidValidateResult();
        }

        return new ErrorValidateResult(
            'string.notAllowed',
            ['options' => $this->options],
        );
    }
}
