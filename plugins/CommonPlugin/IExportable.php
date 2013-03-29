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
 * @version   SVN: $Id: IExportable.php 716 2012-03-28 16:17:03Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This is an interface for classes that can export their results
 * 
 */
interface CommonPlugin_IExportable
{
	public function exportFileName();
	public function exportRows();
	public function exportFieldNames();
	public function exportValues(array $row);
}
