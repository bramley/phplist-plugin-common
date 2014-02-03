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
 * This class combines the Pager and WebblerListing objects
 * 
 */
class CommonPlugin_Listing 
{
    private $controller;
    private $populator;

    public $noResultsMessage = 'no_results';
    public $pager;
    public $sort = false;

    public function __construct(CommonPlugin_Controller $controller, CommonPlugin_IPopulator $populator)
    {
        $this->controller = $controller;
        $this->populator = $populator;
        $this->pager = new CommonPlugin_Pager($controller);
    }

    public function display()
    {
        $total = $this->populator->total();
        list($start, $limit) = $this->pager->range($total);

        $w = new CommonPlugin_WebblerListing();
        $w->usePanel($this->pager->display());

        if ($this->sort) {
            $w->addSort();
        }

        if ($total == 0) {
            $w->addElement($this->controller->i18n->get($this->noResultsMessage));
        }
        $this->populator->populate($w, $start, $limit);
        return $w->display();
    }
}
