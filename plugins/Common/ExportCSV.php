<?php

namespace phpList\plugin\Common;

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

/**
 * This class manages the export in CSV format.
 */
class ExportCSV
{
    public function __construct()
    {
    }

    public function export(IExportable $exporter)
    {
        $fileName = $exporter->exportFileName();

        ob_end_clean();
        header('Content-Type: text/csv');
        header(sprintf('Content-Disposition: attachment; filename="%s.csv"', $fileName));
        $out = fopen('php://output', 'w');
        fputcsv($out, $exporter->exportFieldNames());
        $rows = $exporter->exportRows();

        foreach ($rows as $row) {
            fputcsv($out, $exporter->exportValues($row));
        }

        fclose($out);
    }
}
