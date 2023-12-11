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

class DatabaseCache
{
    private $cache;

    public function __construct($name = 'cache', $defaultTTL = 3600 * 24)
    {
        global $database_host, $database_name, $database_port, $database_user, $database_password, $table_prefix;

        $dsn = sprintf('mysql:dbname=%s;charset=%s;host=%s;port=%s', $database_name, 'utf8mb4', $database_host, $database_port);
        $pdo = new \PDO($dsn, $database_user, $database_password);
        $table = $table_prefix . $name;
        $this->cache = new \Kodus\Cache\DatabaseCache($pdo, $table, $defaultTTL);
    }

    public function __call($method, array $parameters)
    {
        return $this->cache->{$method}(...$parameters);
    }
}
