<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2020 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */
$limit = isset($_GET['limit']) && ctype_digit($_GET['limit']) ? $_GET['limit'] : 5000;
$sql =
    "SELECT *
    FROM(
        SELECT id, entered, page, entry
        FROM {$tables['eventlog']}
        ORDER BY id DESC
        LIMIT $limit
    ) AS t
    ORDER BY id ASC";
$result = Sql_Query($sql);

ob_end_clean();
header('Content-type: text/csv');
header('Content-disposition:  attachment; filename=eventlog.csv');

$handle = fopen('php://output', 'w');
fputcsv($handle, ['id', 'entered', 'page', 'entry']);

while ($row = mysqli_fetch_row($result)) {
    fputcsv($handle, $row);
}
fclose($handle);
exit;
