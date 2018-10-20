<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Common\DAO;

/**
 * Trait that provides access to the attribute table.
 */
trait AttributeTrait
{
    private $attrNameLength;
    private $maxAttrs;

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
         *    need to unescape attribute name
         */
        $limit = $this->maxAttrs > 0 ? "LIMIT $this->maxAttrs" : '';
        $sql =
            "SELECT id, 
            LEFT(REPLACE(
                REPLACE(name, '\\\\\\'', '\\''),
                '\\\\\\\\', '\\\\'
            ), $this->attrNameLength) AS name,
            type, tablename 
            FROM {$this->tables['attribute']} 
            ORDER BY listorder
            $limit";

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
