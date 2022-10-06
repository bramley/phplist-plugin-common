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
 * Trait providing access to the message table.
 */
trait MessageTrait
{
    private $uuidVersion = '3.3.0';

    public function messageById($msgid)
    {
        $sql =
            "SELECT *
            FROM {$this->tables['message']} m
            WHERE m.id = $msgid";

        return $this->dbCommand->queryRow($sql);
    }

    public function messageByUuid($uuid)
    {
        $uuid = sql_escape($uuid);
        $sql =
            "SELECT *
            FROM {$this->tables['message']}
            WHERE uuid = '$uuid'";

        return $this->dbCommand->queryRow($sql);
    }

    /**
     * Create a row in the message table populated with fields from an existing row and a
     * generated UUID.
     * Create rows in the messagedata table populated from existing rows, and allow plugins
     * to copy additional rows.
     * Copy rows from the listmessage table.
     *
     * @param int $id the message id
     *
     * @return int the id of the created message
     */
    public function copyMessage($id)
    {
        global $plugins;

        $sql = "
            INSERT INTO {$this->tables['message']}
            (id, subject, fromfield, tofield, replyto, message, textmessage, footer,
                entered, modified, embargo, repeatinterval, repeatuntil,
                status, htmlformatted, sendformat, template, owner, requeueinterval, requeueuntil
            )
            SELECT NULL, CONCAT('Copy - ', subject), fromfield, tofield, replyto, message, textmessage, footer,
                now(), now(), embargo, repeatinterval, repeatuntil,
                'draft', htmlformatted, sendformat, template, owner, requeueinterval, requeueuntil
            FROM {$this->tables['message']}
            WHERE id = $id";
        $newId = $this->dbCommand->queryInsertId($sql);

        if (version_compare(getConfig('version'), $this->uuidVersion) >= 0) {
            $uuid = \UUID::generate(4);
            $sql = "
                UPDATE {$this->tables['message']}
                SET uuid = '$uuid'
                WHERE id = $newId";
            $this->dbCommand->queryAffectedRows($sql);
        }
        $rowNames = array('sendmethod', 'sendurl', 'campaigntitle', 'excludelist');

        foreach ($plugins as $pi) {
            if (method_exists($pi, 'copyCampaignHook')) {
                $rowNames = array_merge($rowNames, $pi->copyCampaignHook());
            }
        }
        $inList = implode(
            ', ',
            array_map(
                function ($item) {
                    return "'" . sql_escape($item) . "'";
                },
                $rowNames
            )
        );
        $sql = "
            INSERT INTO {$this->tables['messagedata']}
            (name, id, data)
            SELECT name, $newId, data
            FROM {$this->tables['messagedata']}
            WHERE id = $id AND name IN ($inList)";
        $this->dbCommand->queryAffectedRows($sql);

        $sql = "
            INSERT INTO {$this->tables['listmessage']}
            (messageid, listid, entered)
            SELECT $newId, listid, now()
            FROM {$this->tables['listmessage']}
            WHERE messageid = $id";
        $this->dbCommand->queryAffectedRows($sql);

        return $newId;
    }

    public function deleteMessage($id)
    {
        global $plugins;

        $sql = "
            DELETE FROM {$this->tables['message']}
            WHERE id=$id";
        $count = $this->dbCommand->queryAffectedRows($sql);

        if ($count == 0) {
            return false;
        }
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
        $sql =
            "DELETE FROM {$this->tables['user_message_view']}
            WHERE messageid = $id";
        $count = $this->dbCommand->queryAffectedRows($sql);

        foreach ($plugins as $pi) {
            if (method_exists($pi, 'deleteCampaignHook')) {
                $pi->deleteCampaignHook($id);
            }
        }

        return true;
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

    public function suspendMessage($id)
    {
        $sql =
            "UPDATE {$this->tables['message']}
            SET status = 'suspended'
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

    public function deleteNotSent($id, $subscribers)
    {
        $rows = 0;

        foreach ($subscribers as $userId) {
            $sql = "DELETE FROM {$this->tables['usermessage']}
                WHERE status = 'not sent'
                AND messageid = $id
                AND userid = $userId
            ";
            $rows += $this->dbCommand->queryAffectedRows($sql);
        }

        return $rows;
    }

    public function submitCampaign($messageId)
    {
        Sql_Query(
            "UPDATE {$this->tables['message']}
            SET status = 'submitted'
            WHERE (status = 'sent' OR status = 'draft') AND id = $messageId"
        );

        return Sql_Affected_Rows() > 0;
    }

    public function messageData($messageId, $name)
    {
        $sql =
            "SELECT data
            FROM {$this->tables['messagedata']} m
            WHERE id = $messageId AND name = '$name'";

        return $this->dbCommand->queryOne($sql);
    }

    public function messageAttachments($messageId)
    {
        $sql =
            "SELECT a.id, filename, remotefile, mimetype, description, size
            FROM {$this->tables['attachment']} a
            JOIN {$this->tables['message_attachment']} ma ON a.id = ma.attachmentid
            WHERE ma.messageid = $messageId";

        return $this->dbCommand->queryAll($sql);
    }
}
