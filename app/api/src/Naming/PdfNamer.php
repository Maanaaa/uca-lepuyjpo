<?php
namespace App\Naming;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

class PdfNamer implements NamerInterface
{
    public function name($object, PropertyMapping $mapping): string
    {
        return sprintf('%s-plan.pdf', $object->getSlug());
    }
}