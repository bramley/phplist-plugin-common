<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2014 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 *  This page displays the php session
 *
 */
ob_start();
var_dump($_SESSION);
echo '<pre>' . htmlspecialchars(ob_get_clean()) . '</pre>';
