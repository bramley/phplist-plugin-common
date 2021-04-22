<?php
namespace Crossjoin\Css\Format\Rule\AtFontFace;

use Crossjoin\Css\Format\Rule\DeclarationAbstract;

class FontFaceDeclaration
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
        if (parent::checkProperty($property)) {
            if (preg_match(
                '/^(?:src|unicode-range|font-(?:family|variant|feature-settings|stretch|weight|style))$/D',
                $property)) {
                return true;
            } else {
                $this->setIsValid(false);
                $this->addValidationError("Invalid property '$property' for @font-face declaration.");
            }
        }

        return false;
    }
}