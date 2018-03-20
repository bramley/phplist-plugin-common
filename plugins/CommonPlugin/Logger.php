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

/**
 * This class extends KLogger to provide configuration through config.php entries.
 * It over-rides the log() method to include the calling class/method/line number.
 */
class CommonPlugin_Logger extends phpList\plugin\Common\Logger
{
}
