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

    public function __construct(CommonPlugin_BaseController $controller, CommonPlugin_IPopulator $populator)
    {
        $this->controller = $controller;
        $this->populator = $populator;
        $this->pager = new CommonPlugin_Pager($controller);
    }

    public function display()
    {
        $total = $this->populator->total();
        list($start, $limit) = $this->pager->range($total);
        $pager = $this->pager->display();

         if ($total > 0) {
            $w = new CommonPlugin_WebblerListing();
            $w->usePanel($pager);

            if ($this->sort)
                $w->addSort();
            $this->populator->populate($w, $start, $limit);
            $result = $w->display();
        } else {
            $result = CHtml::tag('p', array(), $this->controller->i18n->get($this->noResultsMessage));
        }
        return $result;
    }
}
