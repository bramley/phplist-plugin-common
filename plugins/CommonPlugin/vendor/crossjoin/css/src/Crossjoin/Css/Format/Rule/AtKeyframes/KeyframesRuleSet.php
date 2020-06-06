<?php
namespace Crossjoin\Css\Format\Rule\AtKeyframes;

use Crossjoin\Css\Format\Rule\DeclarationAbstract;
use Crossjoin\Css\Format\Rule\RuleAbstract;
use Crossjoin\Css\Format\Rule\TraitDeclarations;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;

class KeyframesRuleSet
extends RuleAbstract
{
    use TraitDeclarations;

    /**
     * @var KeyframesKeyframe[]
     */
    protected $keyframes = [];

    /**
     * @param string $keyframeList
     * @param StyleSheet $styleSheet
     */
    public function __construct($keyframeList = "", StyleSheet $styleSheet = null)
    {
        if ($styleSheet !== null) {
            $this->setStyleSheet($styleSheet);
        }
        if ($keyframeList !== "") {
            $keyframeList = Placeholder::replaceStringsAndComments($keyframeList);
            $this->parseKeyframeList($keyframeList);
        }
    }

    /**
     * Sets the keyframes.
     *
     * @param KeyframesKeyframe[]|KeyframesKeyframe $keyframes
     * @return $this
     */
    public function setKeyframes($keyframes)
    {
        $this->keyframes = [];
        if (!is_array($keyframes)) {
            $keyframes = [$keyframes];
        }
        foreach ($keyframes as $keyframe) {
            $this->addKeyframe($keyframe);
        }

        return $this;
    }

    /**
     * Adds a keyframe.
     *
     * @param KeyframesKeyframe $keyframe
     * @return $this
     */
    public function addKeyframe(KeyframesKeyframe $keyframe)
    {
        $this->keyframes[] = $keyframe;

        return $this;
    }

    /**
     * Gets the keyframes.
     *
     * @return KeyframesKeyframe[]
     */
    public function getKeyframes()
    {
        return $this->keyframes;
    }

    /**
     * Adds a keyframes declaration.
     *
     * @param KeyframesDeclaration $declaration
     * @return $this
     */
    public function addDeclaration(DeclarationAbstract $declaration)
    {
        // TODO Selectors (or keyframes in this case) do NOT cascade!
        // The last one overwrites previous ones
        // @see: https://developer.mozilla.org/en-US/docs/Web/CSS/@keyframes#When_a_keyframe_is_defined_multiple_times

        if ($declaration instanceof KeyframesDeclaration) {
            $this->declarations[] = $declaration;
        } else {
            throw new \InvalidArgumentException(
                "Invalid declaration instance. Instance of 'KeyframesDeclaration' expected."
            );
        }

        return $this;
    }

    /**
     * Parses a string of keyframes.
     *
     * @param string $keyframeList
     */
    protected function parseKeyframeList($keyframeList)
    {
        foreach (explode(",", $keyframeList) as $keyframe) {
            $keyframe = trim($keyframe, " \r\n\t\f");
            $this->addKeyframe(new KeyframesKeyframe($keyframe, $this->getStyleSheet()));
        }
    }
}