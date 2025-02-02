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
class KeyedIterator extends \IteratorIterator
{
    private $keyColumn;

    /**
     * @param Iterator $iterator
     * @param string   $keyColumn
     */
    public function __construct($iterator, $keyColumn)
    {
        parent::__construct($iterator);
        $this->keyColumn = $keyColumn;
    }

    /**
     * Return the key as a column from the result.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return parent::current()[$this->keyColumn];
    }
}
