<?php
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

namespace phpList\plugin\Common;

/**
 * This class combines the Pager and WebblerListing objects.
 */
class Listing
{
    private $populator;

    public $noResultsMessage = 'no_results';
    public $pager;
    public $sort = false;

    /**
     * For backward compatibility the constructor has two signatures.
     *
     * new Listing(IPopulator $populator)
     * new Listing(ignored, IPopulator $populator)
     */
    public function __construct()
    {
        $this->populator = func_num_args() == 1 ? func_get_arg(0) : func_get_arg(1);
        $this->pager = new Pager();
    }

    public function display()
    {
        $total = $this->populator->total();

        $w = new WebblerListing();
        $w->usePanel($this->pager->display($total));

        if ($this->sort) {
            $w->addSort();
        }

        if ($total == 0) {
            $w->addElement(s($this->noResultsMessage));
        }
        list($start, $limit) = $this->pager->range();
        $this->populator->populate($w, $start, $limit);

        return $w->display();
    }
}
