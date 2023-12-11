<?php
/**
 * Cache plugin for phplist.
 *
 * This file is a part of Cache plugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2023 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Common;

class Cache
{
    protected static $instance = null;

    private function __construct()
    {
    }

    public static function instance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new DatabaseCache();
        }

        return static::$instance;
    }
}
