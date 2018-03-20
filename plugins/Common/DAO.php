<?php

namespace phpList\plugin\Common;

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
 * Base DAO class.
 */
class DAO
{
    protected $dbCommand;
    protected $tables;
    protected $table_prefix;

    /*
     * Public methods
     */
    public function __construct($dbCommand)
    {
        global $tables;
        global $table_prefix;

        $this->dbCommand = $dbCommand;
        $this->tables = $tables;
        $this->table_prefix = $table_prefix;
    }
}
