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

namespace phpList\plugin\Common;

abstract class Controller extends BaseController
{
    /*
     *    Public attributes
     */
    public $i18n;
    public $logger;

    /*
     *    Private methods
     */
    private function stripSlashes(&$val)
    {
        $val = stripslashes($val);
    }

    /*
     *    Protected methods
     */
    protected function actionHelp()
    {
        if (isset($_GET['topic'])) {
            $help = new HelpManager($this);
            $help->display($_GET['topic']);
        }
        exit;
    }

    protected function actionChart()
    {
        if (isset($_GET['chartID'])) {
            $chart = new GoogleChart();
            $chart->sendChart($_GET['chartID']);
        }
        exit;
    }

    protected function actionExportCSV(IExportable $exportable = null)
    {
        if ($exportable === null) {
            $exportable = $this;
        }
        $exporter = new ExportCSVAsync();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (isset($_GET['stage'])) {
            switch ($_GET['stage']) {
                case 'build':
                   $exporter->export($exportable);
                   break;
                case 'send':
                   $exporter->send($exportable);
                   break;
                case 'progress':
                   $exporter->progress();
                   break;
            }
        } else {
            $exporter->start();
        }
        exit;
    }

    protected function normalise(&$post)
    {
        array_walk_recursive($post, array($this, 'stripSlashes'));
    }

    protected function logEvent($message)
    {
        global $page;

        $currentPage = $page;
        $page = $_GET['pi'];
        logEvent($message);
        $page = $currentPage;
    }

    /*
     *    Public methods
     */
    public function __construct()
    {
        parent::__construct();
        $this->i18n = I18N::instance();
        $this->logger = Logger::instance();
    }

    public function run($action = null)
    {
        if (!isset($action)) {
            $action = 'default';
        }

        $method = 'action' . ucfirst($action);
        $this->$method();
    }
}
