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
 * DAO class providing access to the message table
 * 
 */
class CommonPlugin_DAO_Message extends CommonPlugin_DAO
{
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
                status,htmlformatted,sendformat, template,owner
            )
            SELECT NULL, CONCAT('Copy - ', subject), fromfield, tofield, replyto, message, textmessage, footer, now(), now(), now(), now(),
                'draft', htmlformatted, sendformat, template, owner
            FROM {$this->tables['message']}
            WHERE id=$id";
         $id = $this->dbCommand->queryInsertId($sql);

        return $id;
    }

    public function deleteMessage($id)
    {
        $sql = "
            DELETE FROM {$this->tables['message']}
            WHERE id=$id";
         $count = $this->dbCommand->queryAffectedRows($sql);

        if ($count > 0) {
            $sql = "
                DELETE FROM {$this->tables['usermessage']}
                WHERE messageid = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            $sql = "
                DELETE FROM {$this->tables['listmessage']}
                WHERE messageid = $id";
            $count = $this->dbCommand->queryAffectedRows($sql);
            /* linktrack
            linktrack_userclick
            messagedata
            message_attachment
            user_message_bounce
            user_message_forward
            */
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
            WHERE id = $id";
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
