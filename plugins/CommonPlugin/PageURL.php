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
	 *	Private methods
	 */
	private function __construct()
	{
	}
	/*
	 *	Public methods
	 */
	public static function create($page = null, array $params = array())
	{
		$p = array();

		if ($page) {
			$p['page'] = $page;
		} else {
			$p['page'] = $_GET['page'];
			$p['pi'] = $_GET['pi'];
		}

		return './?' . http_build_query($p + $params, '', '&');
	}
}
