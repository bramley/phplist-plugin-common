<?php
namespace Crossjoin\Css\Format\Rule\AtFontFace;

use Crossjoin\Css\Format\Rule\AtRuleAbstract;
use Crossjoin\Css\Format\Rule\DeclarationAbstract;
use Crossjoin\Css\Format\Rule\TraitDeclarations;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;

class FontFaceRule
extends AtRuleAbstract
{
    use TraitDeclarations;

    /**
     * @param string|null $ruleString
     * @param StyleSheet|null $styleSheet
     */
    public function __construct($ruleString = null, StyleSheet $styleSheet = null)
    {
        if ($styleSheet !== null) {
            $this->setStyleSheet($styleSheet);
        }
        if ($ruleString !== null) {
            // The rule string doesn't contain any other information than the at-rule,
            // but as all rules accept a rule string and this could be extended in a
            // future CSS version, it's also present here...
        }
    }

    /**
     * Adds a declaration to the rule.
     *
     * @param FontFaceDeclaration $declaration
     * @return $this
     */
    public function addDeclaration(DeclarationAbstract $declaration)
    {
        if ($declaration instanceof FontFaceDeclaration) {
            $this->declarations[] = $declaration;
        } else {
            throw new \InvalidArgumentException(
                "Invalid declaration instance. Instance of 'FontFaceDeclaration' expected."
            );
        }

        return $this;
    }
}