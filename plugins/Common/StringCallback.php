<?php
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

namespace phpList\plugin\Common;

class StringCallback
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function __toString()
    {
        $callback = $this->callback;

        return $callback();
    }
}
