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
 * trait providing methods on the list table.
 */
trait ListsTrait
{
    public function listById($listID)
    {
        $sql =
            "SELECT REPLACE(l.name, '&amp;', '&') as name, l.description, l.active
            FROM {$this->tables['list']} l
            WHERE id = $listID";

        return $this->dbCommand->queryRow($sql);
    }

    public function listsForOwner($loginid)
    {
        $owner = $loginid ? 'WHERE l.owner = ' . $loginid : '';
        $sql =
            "SELECT l.id, REPLACE(l.name, '&amp;', '&') as name, l.description, l.active
            FROM {$this->tables['list']} l
            $owner
            ORDER BY l.listorder";

        return $this->dbCommand->queryAll($sql);
    }

    public function listsForMessage($msgid, $column = null)
    {
        $sql =
            "SELECT l.id, REPLACE(l.name, '&amp;', '&') AS name, l.description, l.active
            FROM {$this->tables['listmessage']} lm
            JOIN {$this->tables['list']} l ON lm.listid = l.id
            WHERE lm.messageid = $msgid";

        return $column
            ? $this->dbCommand->queryColumn($sql, $column)
            : $this->dbCommand->queryAll($sql);
    }
}
