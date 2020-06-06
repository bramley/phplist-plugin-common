<?php
namespace Crossjoin\Css\Format\StyleSheet;

use Crossjoin\Css\Format\Rule\HasRulesInterface;
use Crossjoin\Css\Format\Rule\TraitRules;

class StyleSheet
implements HasRulesInterface
{
    use TraitRules;

    /**
     * @var string|null Charset of the style sheet
     */
    protected $charset;

    /**
     * Sets the charset for the style sheet.
     *
     * @param string $charset
     * @return $this
     */
    public function setCharset($charset)
    {
        if (is_string($charset)) {
            if (in_array($charset, mb_list_encodings())) {
                $this->charset = $charset;
            } else {
                throw new \InvalidArgumentException(
                    "Invalid value '" . $charset . "' for argument 'charset' given. Charset not supported."
                );
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($charset). "' for argument 'charset' given."
            );
        }

        return $this;
    }

    /**
     * Gets the charset for the style sheet.
     *
     * @return string
     */
    public function getCharset()
    {
        if ($this->charset === null) {
            $this->charset = "UTF-8";
        }

        return $this->charset;
    }
}
