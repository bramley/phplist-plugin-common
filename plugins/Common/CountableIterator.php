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
 * @copyright 2025 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */
class CountableIterator extends \IteratorIterator implements \Countable
{
    private $count;

    /**
     * @param Iterator $iterator
     * @param int      $count
     */
    public function __construct($iterator, $count)
    {
        parent::__construct($iterator);
        $this->count = $count;
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
