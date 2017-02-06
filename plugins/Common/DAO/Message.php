<?php

namespace phpList\plugin\Common\DAO;

use phpList\plugin\Common;

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
 * DAO class providing access to the message table
 * 
 */
class Message extends Common\DAO
{
    const UUID_VERSION = '3.3.0';

    public function messageById($msgid)
    {
        $sql =
            "SELECT *
            FROM {$this->tables['message']} m
            WHERE m.id = $msgid";

        return $this->dbCommand->queryRow($sql);
    }

    public function copyMessage($id)
    {
        $sql = "
            INSERT INTO {$this->tables['message']} 
            (id, subject, fromfield, tofield, replyto, message, textmessage, footer, entered, modified, embargo,repeatuntil,
                status, htmlformatted, sendformat, template, owner
            )
            SELECT NULL, CONCAT('Copy - ', subject), fromfield, tofield, replyto, message, textmessage, footer, now(), now(), now(), now(),
                'draft', htmlformatted, sendformat, template, owner
            FROM {$this->tables['message']}
            WHERE id = $id";
        $newId = $this->dbCommand->queryInsertId($sql);

        if (version_compare(getConfig('version'), self::UUID_VERSION) >= 0) {
            $uuid = \UUID::generate(4);
            $sql = "
                UPDATE {$this->tables['message']}
                SET uuid = '$uuid'
                WHERE id = $newId";
            $this->dbCommand->queryAffectedRows($sql);
        }

        return $newId;
    }

    public function deleteMessage($id)
    {
        $sql = "
            DELETE FROM {$this->tables['message']}
            WHERE id=$id";
        $count = $this->dbCommand->queryAffectedRows($sql);

        if ($count > 0) {
            $sql =
                "DELETE FROM {$this->tables['usermessage']}
                WHERE messageid = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql =
                "DELETE FROM {$this->tables['listmessage']}
                WHERE messageid = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql =
                "DELETE FROM {$this->tables['linktrack_ml']}
                WHERE messageid = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql =
                "DELETE FROM {$this->tables['linktrack_uml_click']}
                WHERE messageid = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql =
                "DELETE FROM {$this->tables['messagedata']}
                WHERE id = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql =
                "DELETE FROM {$this->tables['message_attachment']}
                WHERE messageid = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql =
                "DELETE FROM {$this->tables['user_message_bounce']}
                WHERE message = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql =
                "DELETE FROM {$this->tables['user_message_forward']}
                WHERE message = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            return true;
        } else {
            return false;
        }
    }

    public function requeueMessage($id)
    {
        $sql =
            "UPDATE {$this->tables['message']}
            SET status = 'submitted', sendstart = now() 
            WHERE id = $id AND status IN ('sent', 'suspended')";
        $count = $this->dbCommand->queryAffectedRows($sql);

        return $count;
    }

    public function deleteDraftMessages()
    {
        $sql = "
           DELETE FROM {$this->tables['message']}
           WHERE status = 'draft' AND (subject = '' OR subject = '(no subject)')";
        $count = $this->dbCommand->queryAffectedRows($sql);
        return $count;
    }
}
