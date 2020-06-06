<?php
namespace Crossjoin\Css\Format\Rule\AtPage;

use Crossjoin\Css\Format\Rule\DeclarationAbstract;

class PageDeclaration
extends DeclarationAbstract
{
    /**
     * Checks the declaration property.
     *
     * @param string $property
     * @return bool
     */
    public function checkProperty(&$property)
    {
        // TODO: Add checks
        return parent::checkProperty($property);
    }
}