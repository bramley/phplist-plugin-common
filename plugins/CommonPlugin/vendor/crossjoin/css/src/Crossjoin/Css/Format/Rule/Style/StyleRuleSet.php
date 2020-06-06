<?php
namespace Crossjoin\Css\Format\Rule\Style;

use Crossjoin\Css\Format\Rule\DeclarationAbstract;
use Crossjoin\Css\Format\Rule\RuleAbstract;
use Crossjoin\Css\Format\Rule\RuleGroupableInterface;
use Crossjoin\Css\Format\Rule\TraitDeclarations;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;

class StyleRuleSet
extends RuleAbstract
implements RuleGroupableInterface
{
    use TraitDeclarations;

    /**
     * @var StyleSelector[] Array of selectors for the style rule
     */
    protected $selectors = [];

    /**
     * @param string|null $ruleString
     * @param StyleSheet $styleSheet
     */
    public function __construct($ruleString = null, StyleSheet $styleSheet = null)
    {
        if ($styleSheet !== null) {
            $this->setStyleSheet($styleSheet);
        }
        if ($ruleString !== null) {
            $ruleString = Placeholder::replaceStringsAndComments($ruleString);
            $this->parseRuleString($ruleString);
        }
    }

    /**
     * Sets the selectors for the style rule.
     *
     * @param StyleSelector[]|StyleSelector $selectors
     * @return $this
     */
    public function setSelectors($selectors)
    {
        $this->selectors = [];
        if (!is_array($selectors)) {
            $selectors = [$selectors];
        }
        foreach ($selectors as $selector) {
            $this->addSelector($selector);
        }

        return $this;
    }

    /**
     * Adds a selector for the style rule.
     *
     * @param StyleSelector $selector
     * @return $this
     */
    public function addSelector(StyleSelector $selector)
    {
        $this->selectors[] = $selector;

        return $this;
    }

    /**
     * Gets the selectors for the style rule.
     *
     * @return StyleSelector[]
     */
    public function getSelectors()
    {
        return $this->selectors;
    }

    /**
     * Adds a declaration to the rule.
     *
     * @param StyleDeclaration $declaration
     * @return $this
     */
    public function addDeclaration(DeclarationAbstract $declaration)
    {
        if ($declaration instanceof StyleDeclaration) {
            $this->declarations[] = $declaration;
        } else {
            throw new \InvalidArgumentException(
                "Invalid declaration instance. Instance of 'StyleDeclaration' expected."
            );
        }

        return $this;
    }

    /**
     * Parses the selector rule.
     *
     * @param string $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        foreach ($this->getSelectorStrings($ruleString) as $selectorString)
        {
            // Check for invalid selector (e.g. if starting with a comma, like in this example from
            // the spec ",all { body { background:lime } }")
            if ($selectorString === "") {
                $this->setIsValid(false);
                $this->addValidationError("Invalid selector at '$ruleString'.");
                break;
            }

            $this->addSelector(new StyleSelector($selectorString, $this->getStyleSheet()));
        }
    }

    /**
     * Helper method to parse the style rule.
     *
     * @param string $selectorList
     * @return array
     */
    protected function getSelectorStrings($selectorList)
    {
        $charset = $this->getCharset();

        $groupsOpened = 0;
        $ignoreGroupOpenCloseChar = false;
        $enclosedChar = null;

        $selectors = [];
        $subSelectorList = "";
        $currentSelectors = [""];
        $currentSelectorKeys = [0];
        $lastChar = null;

        if (preg_match('/[^\x00-\x7f]/', $selectorList)) {
            $isAscii = false;
            $strLen  = mb_strlen($selectorList, $charset);
        } else {
            $isAscii = true;
            $strLen = strlen($selectorList);
        }

        for ($i = 0, $j = $strLen; $i < $j; $i++) {
            if ($isAscii === true) {
                $char = $selectorList[$i];
            } else {
                $char = mb_substr($selectorList, $i, 1, $charset);
            }

            if ($char === "(") {
                if ($groupsOpened > 0) {
                    $subSelectorList .= $char;
                } else {
                    if ($ignoreGroupOpenCloseChar === false) {
                        foreach ($currentSelectorKeys as $index) {
                            $currentSelectors[$index] .= $char;
                        }
                    }
                }
                $groupsOpened++;
            } else if ($char === ")") {
                $groupsOpened--;
                if ($groupsOpened > 0) {
                    $subSelectorList .= $char;
                } else {
                    if ($subSelectorList != "") {
                        $subSelectors = $this->getSelectorStrings($subSelectorList);
                        $newSelectors = [];
                        foreach ($subSelectors as $subSelector) {
                            foreach ($currentSelectors as $currentSelector) {
                                $concat = $lastChar === "(" ? "" : " ";
                                $newSelectors[] = $currentSelector . $concat . $subSelector;
                            }
                        }
                        $currentSelectors = $newSelectors;
                        $currentSelectorKeys = array_keys($currentSelectors);
                    }

                    if ($ignoreGroupOpenCloseChar === false) {
                        foreach ($currentSelectorKeys as $index) {
                            $currentSelectors[$index] .= $char;
                        }
                    } else {
                        $ignoreGroupOpenCloseChar = false;
                    }
                    $subSelectorList = "";
                }
            } else if ($char === ",") {
                if ($groupsOpened > 0) {
                    $subSelectorList .= $char;
                } else {
                    foreach ($currentSelectors as $currentSelector) {
                        $selectors[] = trim($currentSelector, " \r\n\t\f");
                    }
                    $currentSelectors = [""];
                    $currentSelectorKeys = [0];
                }
            } else if ($char === ":") {
                if ($groupsOpened > 0) {
                    $subSelectorList .= $char;
                } else {
                    if ($isAscii === true) {
                        $nextChars = substr($selectorList, $i);
                    } else {
                        $nextChars = mb_substr($selectorList, $i, null, $charset);
                    }
                    $nextChars = strtolower(preg_replace('/^(\:[-a-z]+)[^-a-z]*.*/', '\\1', $nextChars));

                    if ($nextChars === ":matches") {
                        $i += (9 - 2);
                        $ignoreGroupOpenCloseChar = true;
                    } elseif ($nextChars === ":has") {
                        $i += (5 - 2);
                        $ignoreGroupOpenCloseChar = true;
                    } else {
                        foreach ($currentSelectorKeys as $index) {
                            $currentSelectors[$index] .= $char;
                        }
                    }
                }
            } else {
                if ($groupsOpened > 0) {
                    $subSelectorList .= $char;
                } else {
                    foreach ($currentSelectorKeys as $index) {
                        $currentSelectors[$index] .= $char;
                    }
                }
            }

            // Save last char (to avoid costly mb_substr() call)
            $lastChar = $char;
        }
        foreach ($currentSelectors as $currentSelector) {
            $selectors[] = trim($currentSelector, " \r\n\t\f");
        }

        return $selectors;
    }
}