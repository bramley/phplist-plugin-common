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
 * DAO class providing access to the message table.
 */
class Message extends CommonDAO
{
    use MessageTrait;
}
