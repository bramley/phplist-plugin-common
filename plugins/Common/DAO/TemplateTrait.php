<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2019 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Common\DAO;

/**
 * Trait providing access to the template table.
 */
trait TemplateTrait
{
    public function templateById($id)
    {
        $sql = <<<END
            SELECT id, title, template, listorder
            FROM {$this->tables['template']}
            WHERE id = $id
END;
        if ($row = $this->dbCommand->queryRow($sql)) {
            $row['template'] = stripslashes($row['template']);
            $row['title'] = stripslashes($row['title']);
        }

        return $row;
    }

    public function updateTemplateBody($id, $body)
    {
        $body = sql_escape($body);
        $sql =
            "UPDATE {$this->tables['template']}
            SET template = '$body'
            WHERE id = $id";
        $count = $this->dbCommand->queryAffectedRows($sql);

        return $count;
    }
}
