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
 * @version   SVN: $Id: Listing.php 704 2012-03-22 11:28:29Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
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
	public $sort = true;

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
		$result = $this->pager->display();

 		if ($total > 0) {
			$w = new CommonPlugin_WebblerListing();

			if ($this->sort)
				$w->addSort();
			$this->populator->populate($w, $start, $limit);
			// strip trailing <br> elements from WebblerListing display
			$result .= preg_replace('|(?:<br\s*/?>\s*)+$|', '', $w->display());
		} else {
			$result .= CHtml::tag('p', array(), $this->controller->i18n->get($this->noResultsMessage));
		}
		return $result;
	}
}
