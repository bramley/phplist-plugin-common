<?php

namespace phpList\plugin\Common;

use CHtml;

/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * Class to create a URL for an image served through the plugin
 * Encapsulates how the image is served.
 */
class ImageTag
{
    /*
     *    Public methods
     */
    public function __construct($image, $title)
    {
        $this->image = $image;
        $this->title = $title;
        $this->alt = $title;
    }

    public function __toString()
    {
        $imageUrl = new PageURL('image', array('pi' => 'CommonPlugin', 'image' => $this->image));

        return CHtml::tag('img', array('src' => $imageUrl, 'alt' => $this->alt, 'title' => $this->title));
    }
}
