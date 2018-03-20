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
 * This class provides an interface to the phplist database subroutines.
 */
class DB
{
    /*
     *    Private attributes
     */
    private $logger;

    /*
     *    Private methods
     */
    private function _query($sql)
    {
        /*
         *
         */
        $level = error_reporting(0);
        $time_start = microtime(true);
        $resource = Sql_Query($sql);
        $elapsed = (microtime(true) - $time_start) * 1000;
        $this->logger->debug("elapsed time $elapsed ms\n$sql");
        error_reporting($level);

        if (!$resource) {
            throw new \Exception('A problem with the query: ' . $sql);
        }

        return $resource;
    }

    /*
     *    Public methods
     */
    public function __construct()
    {
        $this->logger = Logger::instance();
    }

    public function queryInsertId($sql)
    {
        /*
         *
         */
        $resource = $this->_query($sql);

        return Sql_Insert_Id();
    }

    public function queryAffectedRows($sql)
    {
        /*
         *
         */
        $resource = $this->_query($sql);

        return Sql_Affected_Rows();
    }

    public function queryAll($sql)
    {
        /*
         *
         */
        return new DBResultIterator($this->_query($sql));
    }

    public function queryRow($sql)
    {
        /*
         *
         */
        $resource = $this->_query($sql);

        return Sql_Fetch_Assoc($resource);
    }

    /**
     * Returns a single value which can be either a named field or the first field.
     *
     * @param string $sql   the query
     * @param string $field a named field to return (optional)
     *
     * @return string|false the field value or false if no rows
     */
    public function queryOne($sql, $field = null)
    {
        $row = $this->queryRow($sql);

        if (!$row) {
            return false;
        }

        if ($field === null) {
            return reset($row);
        }

        return $row[$field];
    }

    public function queryColumn($sql, $field, $index = null)
    {
        /*
         *
         */
        $iterator = $this->queryAll($sql);

        return array_column(iterator_to_array($iterator), $field, $index);
    }
}
