<?php
namespace Crossjoin\Css\Format\Rule\AtMedia;

use Crossjoin\Css\Format\Rule\ConditionAbstract;
use Crossjoin\Css\Format\Rule\TraitConditions;

class MediaQuery
{
    // Common media types
    const TYPE_ALL = "all";
    const TYPE_PRINT = "print";
    const TYPE_SCREEN = "screen";
    const TYPE_SPEECH = "speech";

    // Deprecated media types
    const TYPE_AURAL = "aural";
    const TYPE_BRAILLE = "braille";
    const TYPE_EMBOSSED = "embossed";
    const TYPE_HANDHELD = "handheld";
    const TYPE_PROJECTION = "projection";
    const TYPE_TTY = "tty";
    const TYPE_TV = "tv";

    use TraitConditions;

    /**
     * @var string Media type
     */
    protected $type = self::TYPE_ALL;

    /**
     * @var bool Does the media query contain the 'only' keyword?
     */
    protected $isOnly = false;

    /**
     * @var bool Does the media query contain the 'not' keyword?
     */
    protected $isNot = false;

    /**
     * @param string $type
     */
    public function __construct($type = self::TYPE_ALL)
    {
        $this->setType($type);
    }

    /**
     * Sets the media type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        if (is_string($type)) {
            if (in_array($type, [
                self::TYPE_ALL, self::TYPE_PRINT, self::TYPE_SCREEN, self::TYPE_SPEECH,
                self::TYPE_AURAL, self::TYPE_BRAILLE, self::TYPE_EMBOSSED, self::TYPE_HANDHELD,
                self::TYPE_PROJECTION, self::TYPE_TTY, self::TYPE_TV
            ])) {
                $this->type = $type;

                return $this;
            } else {
                // Invalid type values have to be handled as "not all"
                // @see: http://dev.w3.org/csswg/mediaqueries-4/#error-handling
                $this->type = self::TYPE_ALL;
                $this->setIsNot(true);
                $this->setIsOnly(false);
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($type). "' for argument 'type' given."
            );
        }
    }

    /**
     * Gets the media type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Adds a media rule condition.
     *
     * @param MediaCondition $condition
     * @return $this
     */
    public function addCondition(ConditionAbstract $condition)
    {
        if ($condition instanceof MediaCondition) {
            $this->conditions[] = $condition;
        } else {
            throw new \InvalidArgumentException(
                "Invalid condition instance. Instance of 'MediaCondition' expected."
            );
        }

        return $this;
    }

    /**
     * Sets the 'only' flag.
     *
     * @param bool $isOnly
     * @return $this
     */
    public function setIsOnly($isOnly)
    {
        if (is_bool($isOnly)) {
            $this->isOnly = $isOnly;

            return $this;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($isOnly). "' for argument 'isOnly' given."
            );
        }
    }

    /**
     * Gets the 'only' flag.
     *
     * @return bool
     */
    public function getIsOnly()
    {
        return $this->isOnly;
    }

    /**
     * Sets the 'only' flag.
     *
     * @param bool $isNot
     * @return $this
     */
    public function setIsNot($isNot)
    {
        if (is_bool($isNot)) {
            $this->isNot = $isNot;

            return $this;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($isNot). "' for argument 'isNot' given."
            );
        }
    }

    /**
     * Gets the 'only' flag.
     *
     * @return bool
     */
    public function getIsNot()
    {
        return $this->isNot;
    }
}