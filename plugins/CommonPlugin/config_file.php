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
 *  This page displays the phplist config.php file.
 */
function CommonPlugin_showConfig()
{
    if (isset($_SERVER['ConfigFile']) && is_file($f = $_SERVER['ConfigFile'])
        ||
        is_file($f = '../config/config.php')
    ) {
        // matches a define whose name contains KEY
        $regex1 = '/(define.+KEY.+,\s*)([\'"]).+\2/U';
        // matches an assignment to a variable whose name ends in user or password
        $regex2 = '/((?:user|password)\s*=\s*)(["\'])(.+?)\2/';
        $contents = preg_replace([$regex1, $regex2], '$1$2* removed *$2', file_get_contents($f));
        $r = 'Config file: ' . realpath($f) . "<br>\n";
        $r .= str_replace(
            '<br /></span>',
            "<br /></span>\n",
            highlight_string($contents, true)
        );
    } else {
        $r = 'Cannot find config file';
    }

    return $r;
}
echo CommonPlugin_showConfig();
