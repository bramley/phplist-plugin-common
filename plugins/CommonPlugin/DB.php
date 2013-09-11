<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2012 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class provides an interface to the phplist database subroutines
 * 
 */
class CommonPlugin_DB {
    /*
     *    Private attributes
     */
    private $logger;

    /*
     *    Private methods
     */
    private function _query($sql) {
        /*
         * 
         */
        $time_start = microtime(true);
        $resource = Sql_Query($sql);
        $elapsed = (microtime(true) - $time_start) * 1000;
        $this->logger->log("elapsed time $elapsed ms\n$sql", KLogger::DEBUG);

        if (!$resource) {
            throw new Exception('Invalid query: ' . mysql_error() . ' ' . $sql);
        }
        return $resource;
    }
    /*
     *    Public methods
     */
    public function __construct() {
        $this->logger = CommonPlugin_Logger::instance();
    }

    public function queryInsertId($sql) {
        /*
         * 
         */
        $resource = $this->_query($sql);
        return mysql_insert_id();
    }

    public function queryAffectedRows($sql) {
        /*
         * 
         */
        $resource = $this->_query($sql);
        return Sql_Affected_Rows();
    }

    public function queryAll($sql) {
        /*
         * 
         */
        return new CommonPlugin_DBResultIterator($this->_query($sql));
    }

    public function queryRow($sql) {
        /*
         * 
         */
        $resource = $this->_query($sql);

        return Sql_Fetch_Array($resource);
    }

    public function queryOne($sql, $field) {
        /*
         * 
         */
        $row = $this->queryRow($sql);
        return $row ? $row[$field] : false;
    }

    public function queryColumn($sql, $field) {
        /*
         * 
         */
        $resource = $this->_query($sql);

        $result = array();

        while ($row = Sql_Fetch_Array($resource)) {
            $result[] = $row[$field];
        }
        return $result;
    }

}
