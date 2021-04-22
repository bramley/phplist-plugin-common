<?php
namespace Crossjoin\Css\Format\Rule\AtCharset;

use Crossjoin\Css\Format\Rule\AtRuleAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;

class CharsetRule
extends AtRuleAbstract
{
    /**
     * @var string|null Charset rule value
     */
    protected $value;

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
            $ruleString = Placeholder::replaceStringPlaceholders($ruleString);
            $this->parseRuleString($ruleString);
        }
    }

    /**
     * Sets the value for the charset rule.
     *
     * @param string $value
     * @return $this
     */
    protected function setValue($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            if ($value !== "") {
                $this->value = $value;
            } else {
                throw new \InvalidArgumentException("Invalid value for argument 'value' given.");
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($value) . "' for argument 'value' given. String expected."
            );
        }

        return $this;
    }

    /**
     * Gets the value for the charset rule.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Parses the charset rule.
     *
     * @param string $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        if (is_string($ruleString)) {
            // Parse for @charset rule
            if (preg_match('/^@charset\s+(["\'])([-a-zA-Z0-9_]+)\g{1}/i', rtrim($ruleString), $matches)) {
                $this->setValue($matches[2]);
            } else {
                throw new \InvalidArgumentException("Invalid format for @charset rule.");
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($ruleString) . "' for argument 'ruleString' given. String expected."
            );
        }
    }
}