<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2023 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Common;

class FileCache
{
    private $cache;

    public function __construct($name = 'phplist_cache')
    {
        global $tmpdir;

        $this->cache = new \Kodus\Cache\FileCache($tmpdir . '/' . $name, 3600);
    }

    public function __call($method, array $parameters)
    {
        return $this->cache->{$method}(...$parameters);
    }
}
