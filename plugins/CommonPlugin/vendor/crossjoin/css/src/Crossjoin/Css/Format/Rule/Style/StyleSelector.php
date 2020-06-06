<?php
namespace Crossjoin\Css\Format\Rule\Style;

use Crossjoin\Css\Format\Rule\SelectorAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;

class StyleSelector
extends SelectorAbstract
{
    // CSS1 Pseudo elements
    const PSEUDO_ELEMENT_FIRST_LINE = 'first-line';
    const PSEUDO_ELEMENT_FIRST_LETTER = 'first-letter';

    // CSS 1 Pseudo classes
    const PSEUDO_CLASS_LINK = 'link';
    const PSEUDO_CLASS_VISITED = 'visited';
    const PSEUDO_CLASS_ACTIVE = 'active';

    // CSS 2 Pseudo elements
    const PSEUDO_ELEMENT_AFTER = 'after';
    const PSEUDO_ELEMENT_BEFORE = 'before';

    // CSS 2 Pseudo classes
    const PSEUDO_CLASS_LANG = 'lang';
    const PSEUDO_CLASS_FIRST_CHILD = 'first-child';
    const PSEUDO_CLASS_HOVER = 'hover';
    const PSEUDO_CLASS_FOCUS = 'focus';

    // CSS 3 Pseudo classes
    const PSEUDO_CLASS_ENABLED = 'enabled';
    const PSEUDO_CLASS_DISABLED = 'disabled';
    const PSEUDO_CLASS_ROOT = 'root';
    const PSEUDO_CLASS_TARGET = 'target';
    const PSEUDO_CLASS_CHECKED = 'checked';
    const PSEUDO_CLASS_NTH_CHILD = 'nth-child';
    const PSEUDO_CLASS_NTH_LAST_CHILD = 'nth-last-child';
    const PSEUDO_CLASS_NTH_OF_TYPE = 'nth-of-type';
    const PSEUDO_CLASS_NTH_LAST_OF_TYPE = 'nth-last-of-type';
    const PSEUDO_CLASS_LAST_CHILD = 'last-child';
    const PSEUDO_CLASS_FIRST_OF_TYPE = 'first-of-type';
    const PSEUDO_CLASS_LAST_OF_TYPE = 'last-of-type';
    const PSEUDO_CLASS_ONLY_CHILD = 'only-child';
    const PSEUDO_CLASS_ONLY_OF_TYPE = 'only-of-type';
    const PSEUDO_CLASS_EMPTY = 'empty';
    const PSEUDO_CLASS_NOT = 'not';

    // CSS 4 Pseudo classes
    const PSEUDO_CLASS_HAS = 'has';
    const PSEUDO_CLASS_LOCAL_LINK = 'local-link';
    const PSEUDO_CLASS_INDETERMINATE = 'indeterminate';
    const PSEUDO_CLASS_IN_RANGE = 'in-range';
    const PSEUDO_CLASS_OUT_OF_RANGE = 'out-of-range';
    const PSEUDO_CLASS_READ_ONLY = 'read-only';
    const PSEUDO_CLASS_READ_WRITE = 'read-write';
    const PSEUDO_CLASS_COLUMN = 'column';
    const PSEUDO_CLASS_NTH_COLUMN = 'nth-column';
    const PSEUDO_CLASS_NTH_LAST_COLUMN = 'nth-last-column';
    const PSEUDO_CLASS_ANY_LINK = 'any-link';
    const PSEUDO_CLASS_SCOPE = 'scope';
    const PSEUDO_CLASS_PLACEHOLDER_SHOWN = 'placeholder-shown';
    const PSEUDO_CLASS_BLANK = 'blank';
    const PSEUDO_CLASS_MATCHES = 'matches';
    const PSEUDO_CLASS_CURRENT = 'current';
    const PSEUDO_CLASS_PAST = 'past';
    const PSEUDO_CLASS_FUTURE = 'future';
    const PSEUDO_CLASS_DEFAULT = 'default';
    const PSEUDO_CLASS_REQUIRED = 'required';
    const PSEUDO_CLASS_OPTIONAL = 'optional';
    const PSEUDO_CLASS_NTH_MATCH = 'nth-match';
    const PSEUDO_CLASS_NTH_LAST_MATCH = 'nth-last-match';
    const PSEUDO_CLASS_DIR = 'dir';
    const PSEUDO_CLASS_DROP = 'drop';
    const PSEUDO_CLASS_VALID = 'valid';
    const PSEUDO_CLASS_INVALID = 'invalid';

    // Unknown pseudo elements/classes
    const PSEUDO_CLASS_UNKNOWN = '';
    const PSEUDO_ELEMENT_UNKNOWN = '';

    /**
     * @var int Selector specificity
     */
    protected $specificity = 0;

    /**
     * @var array Pseudo classes in the selector
     */
    protected $pseudoClasses = [];

    /**
     * @var array Pseudo elements in the selector
     */
    protected $pseudoElements = [];

    /**
     * @var bool Internal flag to mark the selector as analyzed
     */
    protected $analyzed = false;

    /**
     * @param string $value
     * @param StyleSheet|null $styleSheet
     */
    public function __construct($value, StyleSheet $styleSheet = null)
    {
        parent::__construct($value, $styleSheet);
    }

    /**
     * Gets the selector specificity.
     *
     * @return int
     */
    public function getSpecificity()
    {
        $this->analyzeSelector();

        return $this->specificity;
    }

    /**
     * Gets the selector pseudo classes.
     *
     * @return array
     */
    public function getPseudoClasses()
    {
        $this->analyzeSelector();

        return $this->pseudoClasses;
    }

    /**
     * Gets the selector pseudo elements.
     *
     * @return array
     */
    public function getPseudoElements()
    {
        $this->analyzeSelector();

        return $this->pseudoElements;
    }

    /**
     * Analyzes the selector to extract pseudo classes/elements and calculate the specificity.
     */
    protected function analyzeSelector()
    {
        if ($this->analyzed === false) {
            // Calculate specificity
            $selector = $this->getValue();

            // Set flags
            $isEscaped = false;
            $inSelectorPart = false;
            $wasEscaped = false;
            $inAttribute = false;

            $countIdSelectors = 0;
            $countClassSelectors = 0;
            $countAttributeSelectors = 0;
            $countPseudoClassSelectors = 0;
            $countTypeSelectors = 0;
            $countPseudoElementSelectors = 0;
            $countUniversalSelectors = 0;

            $charset = $this->getCharset();

            if (preg_match('/[^\x00-\x7f]/', $selector)) {
                $isAscii = false;
                $strLen = mb_strlen($selector, $charset);
            } else {
                $isAscii = true;
                $strLen = strlen($selector);
            }

            for ($i = 0, $j = $strLen; $i < $j; $i++) {
                if ($isAscii === true) {
                    $char = $selector[$i];
                } else {
                    $char = mb_substr($selector, $i, 1, $charset);
                }

                // Check for escape character
                if ($isEscaped === false && $char === "\\") {
                    $isEscaped = true;
                } else {
                    $wasEscaped = $isEscaped;
                    $isEscaped = false;
                }

                // Filter user-defined strings, that can contain special characters, comments etc.
                // and therefore need to be ignored.
                if ($inAttribute === false && $wasEscaped === false && $char === "[") {
                    $countAttributeSelectors++;
                    $inAttribute = true;
                    continue;
                } else {
                    if ($wasEscaped === false && $char === "]") {
                        $inAttribute = false;
                        continue;
                    }
                }

                if ($inAttribute === false) {
                    if ($char === "#") {
                        $countIdSelectors++;
                        $inSelectorPart = true;
                    } elseif ($char === ".") {
                        $countClassSelectors++;
                        $inSelectorPart = true;
                    } elseif ($char === ":") {
                        if ($isAscii === true) {
                            $nextChars = substr($selector, $i);
                        } else {
                            $nextChars = mb_substr($selector, $i, null, $charset);
                        }
                        $nextChars = preg_replace('/^(\:(?:\:)?[-a-z]+)[^-a-z]*.*/', '\\1', $nextChars);

                        // Check for pseudo element selector
                        if (substr($nextChars, $i, 2) === "::") {
                            $countPseudoElementSelectors++;

                            // Check for pseudo element
                            if ($nextChars === "::first-line") {
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_FIRST_LINE;
                            } elseif ($nextChars === "::first-letter") {
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_FIRST_LETTER;
                                // "after" >= CSS3
                            } elseif ($nextChars === "::after") {
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_AFTER;
                                // "before" >= CSS3
                            } elseif ($nextChars === "::before") {
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_BEFORE;
                                // Other pseudo elements
                            } else {
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_UNKNOWN;
                            }
                            $i += (strlen($nextChars) - 1);

                            $inSelectorPart = true;
                            // Special pseudo class selector
                        } elseif ($nextChars === ":not") {
                            // This pseudo class selector is NOT counted, only the contained
                            // elements are counted!
                            $i += (5 - 1);
                            $inSelectorPart = false;
                            // Other pseudo class selectors
                        } else {
                            $countPseudoClassSelectors++;

                            // Check for pseudo class
                            // (and old pseudo element notation < CSS 3)
                            if ($nextChars === ":link") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_LINK;
                            } elseif ($nextChars === ":visited") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_VISITED;
                            } elseif ($nextChars === ":hover") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_HOVER;
                            } elseif ($nextChars === ":active") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_ACTIVE;
                            } elseif ($nextChars === ":focus") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_FOCUS;
                            } elseif ($nextChars === ":checked") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_CHECKED;
                            } elseif ($nextChars === ":enabled") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_ENABLED;
                            } elseif ($nextChars === ":disabled") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_DISABLED;
                            } elseif ($nextChars === ":target") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_TARGET;
                            } elseif ($nextChars === ":empty") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_EMPTY;
                            } elseif ($nextChars === ":lang") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_LANG;
                            }  elseif ($nextChars === ":root") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_ROOT;
                            } elseif ($nextChars === ":nth-child") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_NTH_CHILD;
                            } elseif ($nextChars === ":nth-last-child") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_NTH_LAST_CHILD;
                            } elseif ($nextChars === ":nth-of-type") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_NTH_OF_TYPE;
                            } elseif ($nextChars === ":nth-last-of-type") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_NTH_LAST_OF_TYPE;
                            } elseif ($nextChars === ":first-child") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_FIRST_CHILD;
                            } elseif ($nextChars === ":last-child") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_LAST_CHILD;
                            } elseif ($nextChars === ":only-child") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_ONLY_CHILD;
                            } elseif ($nextChars === ":only-of-type") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_ONLY_OF_TYPE;
                            } elseif ($nextChars === ":first-of-type") {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_FIRST_OF_TYPE;
                            } elseif ($nextChars === ":after") {
                                // Old pseudo element notation < CSS 3
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_AFTER;
                            } elseif ($nextChars === ":before") {
                                // Old pseudo element notation < CSS 3
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_BEFORE;
                            } elseif ($nextChars === ":first-line") {
                                // Old pseudo element notation < CSS 3
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_FIRST_LINE;
                            } elseif ($nextChars === ":first-letter") {
                                // Old pseudo element notation < CSS 3
                                $this->pseudoElements[] = self::PSEUDO_ELEMENT_FIRST_LETTER;
                            } else {
                                $this->pseudoClasses[] = self::PSEUDO_CLASS_UNKNOWN;
                            }
                            $i += (strlen($nextChars) - 1);

                            $inSelectorPart = true;
                        }
                    } else {
                        if ($char === "*") {
                            $countUniversalSelectors++;
                            $inSelectorPart = true;
                        } else {
                            if ($char === "+" || $char === ">" || $char === "~" || /*preg_match('/\s/', $char)*/
                                $char === " " || $char === "\t" || $char === "\f"
                            ) {
                                $inSelectorPart = false;
                            } else {
                                if ($inSelectorPart === false) {
                                    $countTypeSelectors++;
                                    $inSelectorPart = true;
                                }
                            }
                        }
                    }
                }
            }

            // Set specificity
            $this->specificity += ($countIdSelectors * 100);
            $this->specificity += ($countClassSelectors * 10);
            $this->specificity += ($countAttributeSelectors * 10);
            $this->specificity += ($countPseudoClassSelectors * 10);
            $this->specificity += ($countTypeSelectors * 1);
            $this->specificity += ($countPseudoElementSelectors * 1);
            $this->specificity += ($countUniversalSelectors * 0);

            // Update flag
            $this->analyzed = true;
        }
    }
}