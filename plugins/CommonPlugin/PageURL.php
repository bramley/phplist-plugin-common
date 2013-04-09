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
 * @version   SVN: $Id: PageURL.php 804 2012-07-13 14:18:48Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Convenience class to create a URL to either the current or another phplist page
 * 
 */
class CommonPlugin_PageURL
{
	/*
	 *	Public methods
	 */
	public function __construct($page = null, array $params = array())
	{
        $this->page = $page;
        $this->params = $params;
	}

	public function __toString()
	{
		$p = array();

		if ($this->page) {
			$p['page'] = $this->page;
		} else {
			$p['page'] = $_GET['page'];
			$p['pi'] = $_GET['pi'];
		}

		return './?' . http_build_query($p + $this->params, '', '&');
	}
}
