<?php
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

namespace phpList\plugin\Common;

class ChunkedResultIterator implements \IteratorAggregate
{
    private $chunk;
    private $query;

    /**
     * @param int      $chunk
     * @param callable $query
     */
    public function __construct($chunk, $query)
    {
        $this->chunk = $chunk;
        $this->query = $query;
    }

    /**
     * Create a generator that returns rows from a query by calling the query multiple times, each
     * returning a chunk of the result.
     *
     * @return iterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        $start = 0;

        while (true) {
            $result = ($this->query)($start, $this->chunk);

            if (count($result) == 0) {
                return;
            }

            foreach ($result as $row) {
                yield $row;
            }
            $start += $this->chunk;
        }
    }
}
