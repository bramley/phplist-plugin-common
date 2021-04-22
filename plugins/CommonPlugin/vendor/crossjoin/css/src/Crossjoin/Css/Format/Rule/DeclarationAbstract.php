<?php
namespace Crossjoin\Css\Format\Rule;

use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Format\StyleSheet\TraitStyleSheet;
use Crossjoin\Css\Helper\Placeholder;

abstract class DeclarationAbstract
{
    use TraitStyleSheet;
    use TraitComments;
    use TraitIsValid;

    /**
     * @var string Declaration property
     */
    protected $property;

    /**
     * @var string Declaration value
     */
    protected $value;

    /**
     * @param string $property
     * @param string $value
     * @param StyleSheet $styleSheet
     */
    public function __construct($property, $value, StyleSheet $styleSheet = null)
    {
        if ($styleSheet !== null) {
            $this->setStyleSheet($styleSheet);
        }
        $this->setProperty($property);
        $this->setValue($value);
    }

    /**
     * Sets the declaration property.
     *
     * @param string $property
     */
    protected function setProperty($property)
    {
        if ($this->checkProperty($property)) {
            $this->property = $property;
        }
    }

    /**
     * Sets the declaration property.
     *
     * @param string $property
     * @return bool
     */
    public function checkProperty(&$property)
    {
        if (is_string($property)) {
            $property = Placeholder::replaceStringsAndComments($property);
            $property = Placeholder::removeCommentPlaceholders($property, true);
            $property = Placeholder::replaceStringPlaceholders($property, true);

            return true;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($property). "' for argument 'property' given."
            );
        }
    }

    /**
     * Gets the declaration property.
     *
     * @return string|null
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Sets the declaration value.
     *
     * @param string $value
     */
    protected function setValue($value)
    {
        if ($this->checkValue($value)) {
            $this->value = $value;
        }
    }

    /**
     * Checks the declaration value.
     *
     * @param string $value
     * @return bool
     */
    public function checkValue(&$value)
    {
        if (is_string($value)) {
            $value = Placeholder::replaceStringsAndComments($value);
            $value = Placeholder::removeCommentPlaceholders($value, true);
            $value = Placeholder::replaceStringPlaceholders($value, true);
            $value = trim($value);

            if ($value !== '') {
                return true;
            } else {
                $this->setIsValid(false);
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($value). "' for argument 'value' given."
            );
        }
    }

    /**
     * Gets the declaration value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }
}