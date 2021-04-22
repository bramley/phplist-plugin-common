<?php
namespace Crossjoin\Css\Reader;

class HtmlFile
extends ReaderFileAbstract
{
    /**
     * Gets the CSS content from the HTML source.
     *
     * @return string
     */
    protected function getCssContent()
    {
        $styleString = "";

        if (class_exists("\\DOMDocument")) {
            $doc = new \DOMDocument();
            $doc->loadHTMLFile($this->getFilename());

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