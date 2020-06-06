<?php
namespace Crossjoin\Css\Reader;

class CssString
extends ReaderAbstract
{
    /**
     * @var string|null CSS source content
     */
    protected $content;

    /**
     * @param string $cssContent
     */
    public function __construct($cssContent)
    {
        $this->setCssContent($cssContent);
    }

    /**
     * Sets the CSS source content.
     *
     * @param string $cssContent
     * @return $this
     */
    protected function setCssContent($cssContent)
    {
        if (is_string($cssContent)) {
            $this->content = $cssContent;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($cssContent). "' for argument 'cssContent' given."
            );
        }

        return $this;
    }

    /**
     * Gets the CSS source content.
     *
     * @return string
     */
    protected function getCssContent()
    {
        return $this->content;
    }
}