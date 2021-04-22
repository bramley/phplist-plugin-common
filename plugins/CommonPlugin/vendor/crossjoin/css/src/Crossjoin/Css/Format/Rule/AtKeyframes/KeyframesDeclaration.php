<?php
namespace Crossjoin\Css\Format\Rule\AtKeyframes;

use Crossjoin\Css\Format\Rule\DeclarationAbstract;
use Crossjoin\Css\Helper\Optimizer;

class KeyframesDeclaration
extends DeclarationAbstract
{
    /**
     * Checks the declaration value.
     *
     * @param string $value
     * @return bool
     */
    public function checkValue(&$value)
    {
        if (parent::checkValue($value)) {
            $value = trim($value, " \r\n\t\f;");

            // Check if declaration contains "!important"
            if (strpos($value, "!") !== false) {
                $charset = $this->getStyleSheet()->getCharset();
                if (mb_strtolower(mb_substr($value, -10, null, $charset), $charset) === "!important") {
                    // Invalidate declaration, because "!important" isn't allowed and the declaration should be ignored
                    // @see: https://developer.mozilla.org/en-US/docs/Web/CSS/@keyframes#!important_in_a_keyframe
                    $this->setIsValid(false);
                    $this->addValidationError(
                        "Invalid value '$value' for @keyframes declaration. '!important' not allowed."
                    );
                }
            }

            // Optimize the value
            $value = Optimizer::optimizeDeclarationValue($value);

            return true;
        }

        return false;
    }
}