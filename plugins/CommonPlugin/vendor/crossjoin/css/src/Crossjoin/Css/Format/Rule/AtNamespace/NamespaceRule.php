<?php
namespace Crossjoin\Css\Format\Rule\AtNamespace;

use Crossjoin\Css\Format\Rule\AtRuleAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;
use Crossjoin\Css\Helper\Url;

class NamespaceRule
extends AtRuleAbstract
{
    /**
     * @var string Namespace name
     */
    protected $name;

    /**
     * @var string Namespace prefix
     */
    protected $prefix;

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
     * Sets the namespace name.
     *
     * @param string $name
     * @return $this
     */
    protected function setName($name)
    {
        if (is_string($name)) {
            $this->name = $name;

            return $this;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($name). "' for argument 'name' given."
            );
        }
    }

    /**
     * Gets the namespace name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the namespace prefix.
     *
     * @param string $prefix
     * @return $this
     */
    protected function setPrefix($prefix)
    {
        if (is_string($prefix)) {
            $this->prefix = $prefix;

            return $this;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($prefix). "' for argument 'prefix' given."
            );
        }
    }

    /**
     * Gets the namespace prefix.
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Parses the namespace rule.
     *
     * @param string $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        if (is_string($ruleString)) {
            $charset = $this->getCharset();

            // Remove at-rule and unnecessary white-spaces
            $ruleString = preg_replace('/^[ \r\n\t\f]*@namespace[ \r\n\t\f]+/i', '', $ruleString);
            $ruleString = trim($ruleString, " \r\n\t\f");

            // Remove trailing semicolon
            $ruleString = rtrim($ruleString, ";");

            $isEscaped  = false;
            $inFunction = false;

            $parts = [];
            $currentPart  = "";
            for ($i = 0, $j = mb_strlen($ruleString, $charset); $i < $j; $i++) {
                $char = mb_substr($ruleString, $i, 1, $charset);
                if ($char === "\\") {
                    if ($isEscaped === false) {
                        $isEscaped = true;
                    } else {
                        $isEscaped = false;
                    }
                } else {
                    if ($char === " ") {
                        if ($isEscaped === false) {
                            if ($inFunction == false) {
                                $currentPart = trim($currentPart, " \r\n\t\f");
                                if ($currentPart !== "") {
                                    $parts[] = trim($currentPart, " \r\n\t\f");
                                    $currentPart = "";
                                }
                            }
                        } else {
                            $currentPart .= $char;
                        }
                    } elseif ($isEscaped === false && $char === "(") {
                        $inFunction = true;
                        $currentPart .= $char;
                    } elseif ($isEscaped === false && $char === ")") {
                        $inFunction = false;
                        $currentPart .= $char;
                    } else {
                        $currentPart .= $char;
                    }
                }

                // Reset escaped flag
                if ($isEscaped === true && $char !== "\\") {
                    $isEscaped = false;
                }
            }
            if ($currentPart !== "") {
                $currentPart = trim($currentPart, " \r\n\t\f");
                if ($currentPart !== "") {
                    $parts[] = trim($currentPart, " \r\n\t\f");
                }
            }

            foreach ($parts as $key => $value) {
                $parts[$key] = Placeholder::replaceStringPlaceholders($value);
            }


            $countParts = count($parts);
            if ($countParts === 2) {
                $this->setPrefix($parts[0]);

                // Get URL value
                $name = Url::extractUrl($parts[1]);

                $this->setName($name);
            } elseif ($countParts === 1) {
                // Get URL value
                $name = Url::extractUrl($parts[0]);

                $this->setName($name);
            } else {
                // ERROR
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($ruleString). "' for argument 'ruleString' given."
            );
        }
    }
}