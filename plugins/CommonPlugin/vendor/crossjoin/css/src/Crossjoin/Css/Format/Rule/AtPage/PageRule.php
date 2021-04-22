<?php
namespace Crossjoin\Css\Format\Rule\AtPage;

use Crossjoin\Css\Format\Rule\DeclarationAbstract;
use Crossjoin\Css\Format\Rule\TraitDeclarations;
use Crossjoin\Css\Format\Rule\AtRuleAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;

class PageRule
extends AtRuleAbstract
{
    use TraitDeclarations;

    protected $selector;

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
            $ruleString = Placeholder::replaceStringsAndComments($ruleString);
            $ruleString = Placeholder::removeCommentPlaceholders($ruleString, true);
            $this->parseRuleString($ruleString);
        }
    }

    /**
     * @param PageSelector $selector
     * @return $this
     */
    public function setSelector(PageSelector $selector)
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * @return PageSelector
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * Adds a declaration to the rule.
     *
     * @param PageDeclaration $declaration
     * @return $this
     */
    public function addDeclaration(DeclarationAbstract $declaration)
    {
        if ($declaration instanceof PageDeclaration) {
            $this->declarations[] = $declaration;
        } else {
            throw new \InvalidArgumentException(
                "Invalid declaration instance. Instance of 'PageDeclaration' expected."
            );
        }

        return $this;
    }

    /**
     * Parses the page rule.
     *
     * @param string $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        // Remove at-rule name and unnecessary white-spaces
        $ruleString = preg_replace('/^[ \r\n\t\f]*@page[ \r\n\t\f]*/i', '', $ruleString);
        $ruleString = trim($ruleString, " \r\n\t\f");

        // Extract query list and create a rule from it
        $pageSelector = new PageSelector($ruleString, $this->getStyleSheet());
        $this->setSelector($pageSelector);
    }
}