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
    /**
     * Returns the fields for each attribute keyed by attribute id.
     * Can limit the number of attributes returned and the length of the attribute name.
     *
     * @return array attributes indexed by id
     */
    public function attributesById($attrNameLength = 0, $maxAttrs = 0)
    {
        $limit = $maxAttrs > 0 ? "LIMIT $maxAttrs" : '';
        $sql =
            "SELECT id, name, type, tablename
            FROM {$this->tables['attribute']}
            ORDER BY listorder
            $limit";
        $result = [];

        foreach ($this->dbCommand->queryAll($sql) as $row) {
            $row['name'] = $this->transformAttributeName($row['name'], $attrNameLength);
            $result[$row['id']] = $row;
        }

        return $result;
    }

    /**
     * Now just an alias for attributesById().
     */
    public function attributes(...$params)
    {
        return $this->attributesById(...$params);
    }

    /*
     * Returns the fields for one attribute
     *
     * @return array attribute fields
     */
    public function getAttribute($attr)
    {
        $sql =
            "SELECT id, name, type, tablename
            FROM {$this->tables['attribute']}
            WHERE id = $attr";
        $row = $this->dbCommand->queryRow($sql);
        $row['name'] = $this->transformAttributeName($row['name']);

        return $row;
    }

    private function transformAttributeName($name, $attrNameLength = 0)
    {
        $name = stripslashes($name);

        if ($attrNameLength > 0) {
            if (strlen($name) > $attrNameLength) {
                $leftLength = (int) ($attrNameLength / 2);
                $rightLength = $attrNameLength - $leftLength - 1;
                $name = substr($name, 0, $leftLength) . '…' . substr($name, -$rightLength);
            }
        }

        return $name;
    }
}
