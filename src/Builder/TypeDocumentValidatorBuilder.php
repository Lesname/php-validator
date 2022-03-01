<?php
declare(strict_types=1);

namespace LessValidator\Builder;

use LessDocumentor\Type\Document\TypeDocument;
use LessValidator\Validator;

interface TypeDocumentValidatorBuilder
{
    public function fromTypeDocument(TypeDocument $typeDocument): Validator;
}
