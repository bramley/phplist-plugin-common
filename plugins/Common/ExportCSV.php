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
    /**
     * @param IExportable $exportable
     */
    public function __construct(IExportable $exportable)
    {
        $this->exportable = $exportable;
    }

    /**
     * Export as CSV to the browser.
     */
    public function export()
    {
        $fileName = $this->exportable->exportFileName();

        ob_end_clean();
        header('Content-Type: text/csv');
        header(sprintf('Content-Disposition: attachment; filename="%s.csv"', $fileName));

        $this->exportToFile(fopen('php://output', 'w'));
    }

    /**
     * Export as CSV to a file handle.
     *
     * @param resource $fh
     */
    public function exportToFile($fh)
    {
        fputcsv($fh, $this->exportable->exportFieldNames());

        foreach ($this->exportable->exportRows() as $row) {
            fputcsv($fh, $this->exportable->exportValues($row));
        }
        fclose($fh);
    }
}
