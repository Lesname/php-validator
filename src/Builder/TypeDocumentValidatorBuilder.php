<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use LessDocumentor\Type\Document\TypeDocument;
use LessValidator\Validator;

interface TypeDocumentValidatorBuilder
{
    /**
     * @deprecated
     */
    public function fromTypeDocument(TypeDocument $typeDocument): Validator;

    public function build(): Validator;
}
