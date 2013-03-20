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
 * @version   SVN: $Id: DAO.php 683 2012-03-20 17:30:58Z Duncan $
 * @link      http://forums.phplist.com/
 */

/**
 * Base DAO class
 * 
 */
class CommonPlugin_DAO
{
	protected $dbCommand;
	protected $tables;
	protected $table_prefix;
	/*
	 * Public methods
	 */
	public function __construct($dbCommand)
	{
		global $tables;
		global $table_prefix;

		$this->dbCommand = $dbCommand;
		$this->tables = $tables;
		$this->table_prefix = $table_prefix;
	}
}
