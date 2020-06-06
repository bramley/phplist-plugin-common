<?php
namespace Crossjoin\Css\Format\StyleSheet;

trait TraitStyleSheet
{
    /**
     * @var StyleSheet|null Style sheet
     */
    protected $styleSheet;

    /**
     * Sets the style sheet.
     *
     * @param StyleSheet $styleSheet
     */
    protected function setStyleSheet(StyleSheet $styleSheet)
    {
        $this->styleSheet = $styleSheet;
    }

    /**
     * Gets the style sheet.
     *
     * @return StyleSheet|null
     */
    protected function getStyleSheet()
    {
        return $this->styleSheet;
    }

    /**
     * Gets the style sheet charset (or the default charset).
     * @return string
     */
    protected function getCharset()
    {
        $styleSheet = $this->getStyleSheet();
        if ($styleSheet !== null) {
            return $styleSheet->getCharset();
        } else if ($this->charset === null) {
            return "UTF-8";
        }
    }
}