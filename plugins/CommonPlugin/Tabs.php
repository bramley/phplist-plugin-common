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
 * @version   SVN: $Id: Tabs.php 1233 2013-03-16 11:47:06Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class extends the WebblerTabs class
 * 
 */
class CommonPlugin_Tabs extends WebblerTabs
{
	public function addTab($caption, $url = '', $name = '')
	{
		parent::addTab(htmlspecialchars($caption), htmlspecialchars($url), $name);
    }
}
