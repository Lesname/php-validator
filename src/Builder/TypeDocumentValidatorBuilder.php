<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use LessDocumentor\Type\Document\TypeDocument;
use LessValidator\Validator;

/**
 * @deprecated use ValidatorBuilder
 *
 * @psalm-immutable
 */
interface TypeDocumentValidatorBuilder extends ValidatorBuilder
{
    /**
     * @deprecated
     */
    public function fromTypeDocument(TypeDocument $typeDocument): Validator;
}
