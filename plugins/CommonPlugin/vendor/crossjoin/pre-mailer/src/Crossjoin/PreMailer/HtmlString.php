<?php
namespace Crossjoin\PreMailer;

class HtmlString
extends PreMailerAbstract
{
    /**
     * @var string Content of the HTML source file
     */
    protected $content;

    /**
     * @param string $htmlContent
     */
    public function __construct($htmlContent)
    {
        $this->setHtmlContent($htmlContent);
    }

    /**
     * Sets the HTML content
     *
     * @param string $htmlContent
     * @return $this
     */
    protected function setHtmlContent($htmlContent)
    {
        if (is_string($htmlContent)) {
            $this->content = $htmlContent;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($htmlContent). "' for argument 'htmlContent' given."
            );
        }

        return $this;
    }

    /**
     * Gets the HTML content from the preferred source.
     *
     * @return string
     */
    protected function getHtmlContent()
    {
        return $this->content;
    }
}
