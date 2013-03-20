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
 * @version   SVN: $Id: PageLink.php 758 2012-04-24 15:46:37Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Convenience class to create an HTML link to another page
 * 
 */
class CommonPlugin_PageLink
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

    /**
     * Generate a link for the given page and query parameters
     * @param string $page the page name
     * @param string $text text for link - this is not automatically html encoded
     * @param array $params additional parameters for the URL
     * @return string html <a> element
     * @access private
     */
	public static function create($page, $text, array $params)
	{
		return sprintf(
			"<a href='%s'>%s</a>",
			htmlspecialchars(CommonPlugin_PageURL::create($page, $params)),
			$text
		);
	}
}
?>
