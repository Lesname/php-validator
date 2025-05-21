<?php
declare(strict_types=1);

namespace LesValidator\Composite;

use Override;
use LesDocumentor\Type\Document\Composite\Key\Key;
use LesValidator\ValidateResult\ErrorValidateResult;
use LesValidator\ValidateResult\ValidateResult;
use LesValidator\ValidateResult\ValidValidateResult;
use LesValidator\Validator;
use LesValidator\ValidateResult\Composite\SelfValidateResult;

/**
 * @psalm-immutable
 */
final class PropertyKeysValidator implements Validator
{
    /**
     * @param array<string|Key> $keys
     */
    public function __construct(private readonly array $keys)
    {}

    #[Override]
    public function validate(mixed $input): ValidateResult
    {
        assert(is_array($input));

        $extra = [];

        foreach (array_keys($input) as $key) {
            if (!$this->isKeyAllowed($key)) {
                $extra[] = $key;
            }
        }

        if (count($extra) > 0) {
            return new SelfValidateResult(
                new ErrorValidateResult(
                    'composite.keysNotAllowed',
                    ['extra' => $extra],
                ),
            );
        }

        return new ValidValidateResult();
    }

    private function isKeyAllowed(mixed $key): bool
    {
        if (!is_string($key)) {
            return false;
        }

        foreach ($this->keys as $allowedKey) {
            if ($allowedKey instanceof Key) {
                if ($allowedKey->matches($key)) {
                    return true;
                }
            } elseif ($allowedKey === $key) {
                return true;
            }
        }

        return false;
    }
}
