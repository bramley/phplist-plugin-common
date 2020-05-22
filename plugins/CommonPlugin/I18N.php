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
 * This class provides translation of text strings
 * The same approach as core phplist is used. A language file for each translated language.
 * If a language file does not exist then the class falls-back to English.
 * The language files are in the lan subdirectory of a plugin's main directory.
 */
class CommonPlugin_I18N extends phpList\plugin\Common\I18N
{
}
