<?php
namespace Crossjoin\Css\Format\Rule;

trait TraitIsValid
{
    /**
     * @var bool Validation status
     */
    protected $isValid = true;

    /**
     * @var array Validation errors
     */
    protected $validationErrors = [];

    /**
     * Sets the validation status.
     *
     * @param bool $isValid
     * @return $this
     */
    public function setIsValid($isValid)
    {
        if (is_bool($isValid)) {
            $this->isValid = $isValid;
        } else {
            throw new \InvalidArgumentException(
                "Invalid value '" . $isValid . "' for argument 'isValid' given."
            );
        }

        return $this;
    }

    /**
     * Returns the validation status.
     *
     * @return bool
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Adds a validation error message.
     *
     * @param string $errorMessage
     * @return $this
     */
    public function addValidationError($errorMessage)
    {
        if (is_string($errorMessage)) {
            $this->validationErrors[] = $errorMessage;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($errorMessage). "' for argument 'errorMessage' given."
            );
        }

        return $this;
    }

    /**
     * Gets an array of validation errors.
     *
     * @return string[]
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}