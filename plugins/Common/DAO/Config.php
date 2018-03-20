<?php

namespace phpList\plugin\Common\DAO;

use phpList\plugin\Common;

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
 * DAO class providing access to the config table.
 */
class Config extends Common\DAO
{
    /*
     * Returns the value for a config item
     */
    public function getItem($item)
    {
        $sql =
            "SELECT value
            FROM {$this->tables['config']}
            WHERE item = '$item'";

        return $this->dbCommand->queryOne($sql, 'value');
    }

    /*
     * Sets the value for a config item
     */
    public function setItem($item, $value)
    {
        $item = sql_escape($item);
        $value = sql_escape($value);
        $sql =
            "INSERT INTO {$this->tables['config']} (item, value)
            VALUES('$item', '$value')
            ON DUPLICATE KEY UPDATE value = '$value'";

        return $this->dbCommand->queryAffectedRows($sql);
    }
}
