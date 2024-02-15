<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2024 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Common\DAO;

/*
 * Trait providing methods on the listuser table.
 */
trait ListUserTrait
{
    public function addSubscriberToList($userId, $listId)
    {
        $query = <<<END
            INSERT INTO {$this->tables['listuser']}
            (listid, userid, entered)
            SELECT $listId, $userId, NOW()
            FROM DUAL
            WHERE NOT EXISTS (
                SELECT *
                FROM {$this->tables['listuser']}
                WHERE listid = $listId AND userid = $userId
            )
END;

        return $this->dbCommand->queryAffectedRows($query);
    }

    public function removeSubscriberFromList($userId, $listId)
    {
        $query = <<<END
            DELETE FROM {$this->tables['listuser']}
            WHERE listid = $listId AND userid = $userId
END;

        return $this->dbCommand->queryAffectedRows($query);
    }

    public function removeFromAllLists($userId)
    {
        $sql = <<<END
            DELETE FROM {$this->tables['listuser']}
            WHERE userid = $userId
END;

        return $this->dbCommand->queryAffectedRows($sql);
    }

    public function isUserOnList($userId, $listId)
    {
        $sql = <<<END
            SELECT 1
            FROM {$this->tables['listuser']}
            WHERE userid = $userId AND listid = $listId
END;

        return $this->dbCommand->queryOne($sql);
    }

    public function moveBetweenLists($userId, $fromListId, $toListId)
    {
        $removed = $this->removeSubscriberFromList($userId, $fromListId);
        $added = $this->addSubscriberToList($userId, $toListId);

        return [$removed, $added];
    }
}
