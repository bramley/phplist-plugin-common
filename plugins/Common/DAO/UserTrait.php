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
 * trait providing methods on the user table.
 */
trait UserTrait
{
    public function userByEmail($email)
    {
        $email = sql_escape($email);
        $sql =
            "SELECT * FROM {$this->tables['user']}
            WHERE email = '$email'";

        return $this->dbCommand->queryRow($sql);
    }

    public function userById($id)
    {
        $sql =
            "SELECT * FROM {$this->tables['user']}
            WHERE id = $id";

        return $this->dbCommand->queryRow($sql);
    }

    public function userByUniqid($uid)
    {
        $uid = sql_escape($uid);
        $sql =
            "SELECT * FROM {$this->tables['user']}
            WHERE uniqid = '$uid'";

        return $this->dbCommand->queryRow($sql);
    }

    public function unconfirmUser($email)
    {
        $email = sql_escape($email);
        $sql =
            "UPDATE {$this->tables['user']} u
            SET confirmed = 0
            WHERE email = '$email'";

        return $this->dbCommand->queryAffectedRows($sql);
    }

    public function confirmUser($email)
    {
        $email = sql_escape($email);
        $sql =
            "UPDATE {$this->tables['user']} u
            SET confirmed = 1
            WHERE email = '$email'";

        return $this->dbCommand->queryAffectedRows($sql);
    }

    public function updateUserAttribute($email, $attributeId, $value)
    {
        $sql = <<<END
    UPDATE {$this->tables['user_attribute']} ua
    JOIN  {$this->tables['user']} u ON u.id = ua.userid
    SET ua.value = '$value'
    WHERE u.email = '$email' AND ua.attributeid = $attributeId
END;

        return $this->dbCommand->queryAffectedRows($sql);
    }
}
