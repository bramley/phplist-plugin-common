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

namespace phpList\plugin\Common\DAO;

use phpList\plugin\Common\DAO as CommonDAO;

/**
 * DAO class that provides access to the attribute table.
 */
class Attribute extends CommonDAO
{
    use AttributeTrait;

    public function __construct($dbCommand, $attrNameLength = 20, $maxAttrs = 15)
    {
        $this->attrNameLength = $attrNameLength;
        $this->maxAttrs = $maxAttrs;
        parent::__construct($dbCommand);
    }
}
