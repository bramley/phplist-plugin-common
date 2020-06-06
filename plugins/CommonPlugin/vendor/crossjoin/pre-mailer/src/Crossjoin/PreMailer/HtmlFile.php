<?php
namespace Crossjoin\PreMailer;

class HtmlFile
extends PreMailerAbstract
{
    /**
     * @var string File path of the HTML source file
     */
    protected $filename;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->setFilename($filename);
    }

    /**
     * Sets the file path of the HTML source file.
     *
     * @param string $filename
     * @return $this
     */
    protected function setFilename($filename)
    {
        if (is_string($filename)) {
            if (is_readable($filename)) {
                $this->filename = $filename;
            } elseif (file_exists($filename)) {
                throw new \InvalidArgumentException("File '$filename' isn't readable.");
            } else {
                throw new \InvalidArgumentException("File '$filename' doesn't exist.");
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($filename). "' for argument 'filename' given."
            );
        }

        return $this;
    }

    /**
     * Gets the file path of the HTML source file.
     *
     * @return string
     */
    protected function getFilename()
    {
        return $this->filename;
    }

    /**
     * Gets the HTML content from the preferred source.
     *
     * @return string
     */
    protected function getHtmlContent()
    {
        return file_get_contents($this->getFilename());
    }
}