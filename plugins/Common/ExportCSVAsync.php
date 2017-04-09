<?php

namespace phpList\plugin\Common;

/**
 * CommonPlugin for phplist
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2017 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class manages the export in CSV format
 *
 */
class ExportCSVAsync
{
    /**
     * Generate the html for the progress area.
     */
    public function progress()
    {
        global $img_busy, $pagefooter;

        $src = new PageURL(null, array_merge($_GET, ['stage' => 'build']));
        $html = <<<END
<p>Exporting, this may take a while.</p>
$img_busy
<iframe src="$src" frameborder="0" scrolling="no" width="1" height="1"></iframe>
<script type="text/javascript">
    function progress(text) {
        document.getElementById('done').innerHTML=text;
    }
</script>
<div id="done"></div>
END;
        echo $html;
    }

    /**
     * Export data into a temporary file.
     *
     * @param IExportable $exportable object providing the data to be exported
     */
    public function export(IExportable $exportable)
    {
        global $tmpdir, $installation_name;

        set_time_limit(0);
        ob_implicit_flush(true);
        $output = function ($text) {
            echo sprintf("<script type=\"text/javascript\">parent.progress('%s');</script>\n", $text);
        };
        $tempFile = tempnam($tmpdir, sprintf('%s-export-%s', $installation_name, time()));

        if (false === ($out = fopen($tempFile, 'w'))) {
            $output("Unable to open temporary file $tempFile");

            return;
        }
        fputcsv($out, $exportable->exportFieldNames());
        $rows = $exportable->exportRows();
        $total = count($rows);
        $interval = max((int) ($total / 10), 1);

        foreach ($rows as $i => $row) {
            if ($i % $interval == 0) {
                $percent = $i / $interval * 10;
                $output(sprintf('%d of %d %d%%', $i, $total, $percent));
            }
            fputcsv($out, $exportable->exportValues($row));
        }
        fclose($out);
        $output('Complete');
        $_SESSION[__CLASS__]['file'] = $tempFile;
        $target = new PageURL(null, array_merge($_GET, ['stage' => 'send']));
        echo <<<END
<script type="text/javascript">
var parentJQuery = window.parent.jQuery;
parentJQuery("#busyimage").hide();
document.location = '$target';
</script>
END;
    }

    /**
     * Send the temporary file to the browser.
     *
     * @param IExportable $exportable object providing the data to be exported
     */
    public function send(IExportable $exportable)
    {
        if (!isset($_SESSION[__CLASS__]['file'])) {
            return;
        }
        set_time_limit(0);

        $tempFile = $_SESSION[__CLASS__]['file'];
        $fileName = $exportable->exportFileName();
        Header('Content-type: text/csv');
        Header("Content-disposition:  attachment; filename={$fileName}.csv");
        readfile($tempFile);
        $deleted = unlink($tempFile);

        if ($deleted === false) {
            logEvent("unable to delete temporary file $tempFile");
        }
        unset($_SESSION[__CLASS__]['file']);
    }
}
