<?php
namespace Crossjoin\Css\Reader;

abstract class ReaderFileAbstract
extends ReaderAbstract
{
    /**
     * @var string File path of the CSS source file
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
     * Sets the file path of the CSS source file.
     *
     * @param string $filename
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
    }

    /**
     * Gets the file path of the CSS source file.
     *
     * @return string
     */
    protected function getFilename()
    {
        return $this->filename;
    }
}