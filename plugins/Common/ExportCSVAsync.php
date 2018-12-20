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
class ExportCSVAsync
{
    const PROGRESS_POLL_INTERVAL = 2000;

    /**
     * Write export progress in JSON format.
     *
     * @param string $text   the progress message
     * @param string $status either 'inprogress' or 'complete'
     */
    private function updateProgress($text, $status = 'inprogress')
    {
        session_start();
        $_SESSION['export']['progress'] = json_encode(['status' => $status, 'message' => $text]);
        session_write_close();
    }

    /**
     * Generate the html for the progress area.
     */
    public function start()
    {
        global $img_busy;

        $buildUrl = PageURL::createFromGet(['stage' => 'build']);
        $progressUrl = PageURL::createFromGet(['stage' => 'progress']);
        $format = <<<'END'
<p>%s</p>
%s
<div id="done"></div>
<iframe src="%s" frameborder="0" scrolling="no" width="1" height="1"></iframe>
<script type="text/javascript">
window.timerId = setInterval(
    function () {
        $.getJSON('%s', function(data) {
            console.log(data.status);
            if (data.status == 'complete') {
                clearInterval(window.timerId);
                closedialog();
            }
            $("#done").html(data.message);
        });
    },
    %s
);
</script>
END;
        printf(
            $format,
            s('Exporting, this may take a while'),
            $img_busy,
            $buildUrl,
            $progressUrl,
            self::PROGRESS_POLL_INTERVAL
        );
    }

    /**
     * Export data into a temporary file.
     * Close the session early to allow progress requests to run.
     * When the data has been exported redirect the browser to the 'send' page.
     *
     * @param IExportable $exportable object providing the data to be exported
     */
    public function export(IExportable $exportable)
    {
        global $tmpdir, $installation_name;

        set_time_limit(0);
        $tempFile = tempnam($tmpdir, sprintf('%s-export-%s', $installation_name, time()));

        if (false === ($out = fopen($tempFile, 'w'))) {
            logEvent("Unable to open temporary file $tempFile");

            return;
        }
        $_SESSION[__CLASS__]['file'] = $tempFile;
        session_write_close();

        fputcsv($out, $exportable->exportFieldNames());

        $this->updateProgress(s('Generating the export data'));
        $rows = $exportable->exportRows();
        $total = count($rows);
        $interval = max((int) ($total / 10), 100);

        foreach ($rows as $i => $row) {
            if ($i % $interval == 0) {
                $percent = round($i / $total * 100);
                $this->updateProgress(s('Exported %d of %d %d%%', $i, $total, $percent));
            }
            fputcsv($out, $exportable->exportValues($row));
        }
        fclose($out);
        $this->updateProgress('Finished', 'complete');
        $redirect = PageURL::createFromGet(['stage' => 'send']);
        header('Location: ' . $redirect);
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
        header('Content-Type: text/csv');
        header(sprintf('Content-Disposition: attachment; filename="%s.csv"', $fileName));
        readfile($tempFile);
        $deleted = unlink($tempFile);

        if ($deleted === false) {
            logEvent("unable to delete temporary file $tempFile");
        }
        unset($_SESSION[__CLASS__]);
    }

    public function progress()
    {
        if (isset($_SESSION['export']['progress'])) {
            echo $_SESSION['export']['progress'];
        }
    }
}
