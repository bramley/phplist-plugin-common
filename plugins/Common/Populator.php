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
 * @copyright 2016-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class provides a facade allowing anonymous functions to be used in a IPopulator.
 */
class Populator implements IPopulator
{
    private $populateCallback;
    private $totalCallback;

    /**
     * Constructor.
     *
     * @param callback $populate
     * @param callback $total
     */
    public function __construct($populate, $total)
    {
        $this->populateCallback = $populate;
        $this->totalCallback = $total;
    }

    /**
     * Calls the populate callback.
     *
     * @param WebblerListing $w     the Webbler listing
     * @param int            $start the start index
     * @param int            $limit the number of items to display
     */
    public function populate(\WebblerListing $w, $start, $limit)
    {
        $callback = $this->populateCallback;
        $callback($w, $start, $limit);
    }

    /**
     * Calls the total callback.
     *
     * @return int the total number of items
     */
    public function total()
    {
        $callback = $this->totalCallback;

        return $callback();
    }
}
