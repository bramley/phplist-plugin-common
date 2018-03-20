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
 * This class combines the Pager and WebblerListing objects.
 */
class Listing
{
    private $controller;
    private $populator;

    public $noResultsMessage = 'no_results';
    public $pager;
    public $sort = false;

    public function __construct(Controller $controller, IPopulator $populator)
    {
        $this->controller = $controller;
        $this->populator = $populator;
        $this->pager = new Pager($controller);
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
            $w->addElement($this->controller->i18n->get($this->noResultsMessage));
        }
        list($start, $limit) = $this->pager->range();
        $this->populator->populate($w, $start, $limit);

        return $w->display();
    }
}
