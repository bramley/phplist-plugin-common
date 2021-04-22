<?php
namespace Crossjoin\Css\Format\Rule;

abstract class ConditionAbstract
{
    use TraitIsValid {
        setIsValid as protected;
    }

    /**
     * @var string Condition value
     */
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     * Sets the condition value.
     *
     * @param string $value
     * @return $this
     */
    protected function setValue($value)
    {
        if (is_string($value)) {
            $this->value = $value;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($value). "' for argument 'value' given."
            );
        }

        return $this;
    }

    /**
     * Gets the condition value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }
}