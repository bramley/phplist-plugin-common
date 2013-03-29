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
 * @version   SVN: $Id: Attribute.php 1234 2013-03-17 15:42:12Z Duncan $
 * @link      http://forums.phplist.com/
 */

/**
 * DAO class that provides access to the attribute table
 */
class CommonPlugin_DAO_Attribute extends CommonPlugin_DAO
{
    private $attrNameLength;
    private $maxAttrs;

    public function __construct($dbCommand, $attrNameLength = 20, $maxAttrs = 15) {
        $this->attrNameLength = $attrNameLength;
        $this->maxAttrs = $maxAttrs;
        parent::__construct($dbCommand);
    }
	/*
	 * Returns the fields for each attribute keyed by attribute id
	 */
	public function attributesById()
	{
		$result = array();
		foreach ($this->attributes() as $a) {
			$result[$a['id']] = $a;
		}
		return $result;
	}
	/*
	 * Returns the fields for all attributes
	 */
	public function attributes()
	{
		/*
		 *	need to unescape attribute name
		 */
		$sql = 
			"SELECT id, 
			LEFT(REPLACE(
				REPLACE(name, '\\\\\\'', '\\''),
				'\\\\\\\\', '\\\\'
			), $this->attrNameLength) AS name,
			type, tablename 
			FROM {$this->tables['attribute']} 
			ORDER BY listorder
            LIMIT 0, $this->maxAttrs";

		return $this->dbCommand->queryAll($sql);
	}
	/*
	 * Returns the fields for one attribute
	 */
	public function getAttribute($attr)
	{
		$sql = 
			"SELECT id,
			LEFT(REPLACE(
				REPLACE(name, '\\\\\\'', '\\''),
				'\\\\\\\\', '\\\\'
			), $this->attrNameLength) AS name,
			type, tablename 
			FROM {$this->tables['attribute']} 
			WHERE id = $attr";

		return $this->dbCommand->queryRow($sql);
	}
}
