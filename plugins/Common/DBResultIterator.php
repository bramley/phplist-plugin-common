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

    /**
     * Wraps the result in an interator.
     *
     * @param mysqli_result $result
     */
    public function __construct(\mysqli_result $result)
    {
        parent::__construct($result);
        $this->count = $result->num_rows;
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
