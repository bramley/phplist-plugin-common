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
 */

/**
 * DAO class providing access to the list table
 * 
 */
class CommonPlugin_DAO_List extends CommonPlugin_DAO
{
    public function listById($listID)
    {
        $sql = 
            "SELECT REPLACE(l.name, '&amp;', '&') as name, l.description
            FROM {$this->tables['list']} l
            WHERE id = $listID";

        return $this->dbCommand->queryRow($sql);
    }

    public function listsForOwner($loginid)
    {
        $owner = $loginid ? 'WHERE l.owner = ' . $loginid : '';
        $sql = 
            "SELECT l.id, REPLACE(l.name, '&amp;', '&') as name, l.description
            FROM {$this->tables['list']} l
            $owner
            ORDER BY l.listorder";

        return $this->dbCommand->queryAll($sql);
    }

    public function listsForMessage($msgid)
    {
        $sql = 
            "SELECT REPLACE(l.name, '&amp;', '&') AS name
            FROM {$this->tables['listmessage']} lm
            JOIN {$this->tables['list']} l ON lm.listid = l.id
            WHERE lm.messageid = $msgid";

        return $this->dbCommand->queryColumn($sql, 'name');
    }

}
