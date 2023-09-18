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
 * This class wraps the result of a mysqli query in an iterator.
 */
class DBResultIterator extends \IteratorIterator implements \Countable
{
    private $count;
    private $keyColumn;

    /**
     * Wraps the result in an iterator.
     *
     * @param IteratorAggregate $result
     * @param int               $count
     * @param string            $keyColumn
     */
    public function __construct($result, $count, $keyColumn = null)
    {
        parent::__construct($result);
        $this->count = $count;
        $this->keyColumn = $keyColumn;
    }

    /**
     * Return the key as a column from the result otherwise the default.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        if ($this->keyColumn === null) {
            return parent::key();
        }

        return parent::current()[$this->keyColumn];
    }

    /**
     * Implementation of Countable interface.
     * Returns the number of rows in the result.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->count;
    }
}
