<?php
namespace Crossjoin\Css\Reader;

class HtmlString
extends ReaderAbstract
{
    /**
     * @var string|null HTML source content
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
     * Sets the HTML source content.
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
     * Gets the HTML source content.
     *
     * @return string
     */
    protected function getHtmlContent()
    {
        return $this->content;
    }

    /**
     * Gets the CSS content extracted from the HTML source content.
     *
     * @return string
     */
    protected function getCssContent()
    {
        $styleString = "";

        if (class_exists("\\DOMDocument")) {
            $doc = new \DOMDocument();
            $doc->loadHTML($this->getHtmlContent());

            // Extract styles from the HTML file
            foreach($doc->getElementsByTagName('style') as $styleNode) {
                $styleString .= $styleNode->nodeValue . "\r\n";
            }
        } else {
            throw new \RuntimeException("Required extension 'dom' seems to be missing.");
        }

        return $styleString;
    }
}