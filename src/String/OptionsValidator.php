<?php
declare(strict_types=1);

namespace LesValidator\String;

use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\Validator;

/**
 * @psalm-immutable
 */
final class OptionsValidator implements Validator
{
    /** @var array<string> */
    public readonly array $options;

    /** @param iterable<string> $options */
    public function __construct(iterable $options)
    {
        $arrayOptions = [];

        foreach ($options as $option) {
            $arrayOptions[] = $option;
        }

        $this->options = $arrayOptions;
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
