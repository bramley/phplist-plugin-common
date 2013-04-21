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
 * This class provides an iterator for the result of a db query
 * 
 */
class CommonPlugin_DBResultIterator implements Iterator, Countable
{
    /**
     * Current offset.
     */
    protected $row;
    
    /**
     * MySQL result
     */
    protected $result;
    
    /**
     * Number of rows in result set.
     */
    public $count;
    
    /**
     * Constructor.
     * Stores the query result.
     * 
     * @param resource $result
     */
    public function __construct($resource)
    {    
        $this->result = $resource;
        $this->row = 0;
        $this->count = sql_num_rows($resource);
    }    
    
    public function __destruct()
    {
    }
    
    /**
     * Returns next row of result data.
     * 
     * @return object $row
     */
    public function current()
    {
        return sql_fetch_array($this->result);
    }
        
    /**
     * Returns current row number
     */
    public function key()
    {
        return $this->row;
    }
    
    /** 
     * Increments the row number
     * 
     */
    public function next()
    {
        ++$this->row;
    }
    
    /**
     * Resets the row number and seeks the result back.
     * 
     */
    public function rewind()
    {
        $this->row = 0;
    }
    
    /**
     * Returns whether current row number is a valid row based on total
     * number of available rows in result resource.
     * 
     * @return bool
     */
    public function valid()
    {
        return (bool) ($this->row < $this->count);
    }    

    /**
    * Implementation of Countable interface
    * Returns the number of rows in the result
     * 
     * @return integer
     */
    public function count()
    {
        return $this->count;
    }
}
