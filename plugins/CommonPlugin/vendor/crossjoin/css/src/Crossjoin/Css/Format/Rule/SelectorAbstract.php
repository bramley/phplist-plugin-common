<?php
namespace Crossjoin\Css\Format\Rule;

use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Format\StyleSheet\TraitStyleSheet;
use Crossjoin\Css\Helper\Placeholder;

abstract class SelectorAbstract
{
    use TraitStyleSheet;
    use TraitIsValid {
        setIsValid as protected;
    }

    /**
     * @var string|null Selector value
     */
    protected $value;

    /**
     * @param $value
     * @param StyleSheet $styleSheet
     */
    public function __construct($value, StyleSheet $styleSheet = null)
    {
        if ($styleSheet !== null) {
            $this->setStyleSheet($styleSheet);
        }

        $this->setValue($value);
    }

    /**
     * Sets the selector value.
     *
     * @param string $value
     * @return $this
     */
    protected function setValue($value)
    {
        if ($this->checkValue($value)) {
            $this->value = $value;
        }
        return $this;
    }

    /**
     * Checks the selector value.
     *
     * @param $value
     * @return bool
     */
    public function checkValue(&$value)
    {
        if (is_string($value)) {
            $value = Placeholder::replaceStringsAndComments($value);
            $value = Placeholder::removeCommentPlaceholders($value, true);
            $value = preg_replace('/[ ]+/', ' ', $value);
            $value = Placeholder::replaceStringPlaceholders($value, true);

            return true;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($value). "' for argument 'value' given."
            );
        }
    }

    /**
     * Gets the selector value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}