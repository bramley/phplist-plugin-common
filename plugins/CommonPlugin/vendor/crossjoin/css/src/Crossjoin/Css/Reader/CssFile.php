<?php
namespace Crossjoin\Css\Reader;

class CssFile
extends ReaderFileAbstract
{
    /**
     * Gets the CSS source content.
     *
     * @return string
     */
    protected function getCssContent()
    {
        return file_get_contents($this->getFilename());
    }
}