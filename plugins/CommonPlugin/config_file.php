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
        $r = 'Config file: ' . realpath($f) . "<br>\n";
        $regex = '/((?:user|password)\s*=\s*)(["\'])(.+?)\2/';
        $r .= str_replace(
            '<br /></span>',
            "<br /></span>\n",
            highlight_string(
                preg_replace($regex, '$1$2* removed *$2', file_get_contents($f)),
                true
            )
        );
    } else {
        $r = 'Cannot find config file';
    }

    return $r;
}
echo CommonPlugin_showConfig();
