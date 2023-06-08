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
 *  This page displays the php session.
 */
ob_start();
var_dump($_SESSION);
$output = ob_get_clean();
$xdebugOn = extension_loaded('xdebug') && strpos(ini_get('xdebug.mode'), 'develop') !== false;
echo $xdebugOn ? $output : '<pre>' . htmlspecialchars($output) . '</pre>';
