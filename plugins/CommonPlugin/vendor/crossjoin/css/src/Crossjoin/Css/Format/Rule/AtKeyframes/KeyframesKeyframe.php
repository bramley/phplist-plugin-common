<?php
namespace Crossjoin\Css\Format\Rule\AtKeyframes;

use Crossjoin\Css\Format\Rule\SelectorAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;

class KeyframesKeyframe
extends SelectorAbstract
{
    /**
     * @param string $value
     * @param StyleSheet|null $styleSheet
     */
    public function __construct($value, StyleSheet $styleSheet = null)
    {
        parent::__construct($value, $styleSheet);
    }

    /**
     * Checks the keyframes selector value.
     *
     * @param string $value
     * @return bool
     */
    public function checkValue(&$value)
    {
        if (parent::checkValue($value) === true) {
            if (!preg_match('/^(?:from|to|(?:\d{1,2}|100)%)$/D', $value)) {
                $this->setIsValid(false);
                $this->addValidationError("Invalid value '$value' for the keyframe selector.");

                return false;
            }
            return true;
        }

        return false;
    }
}