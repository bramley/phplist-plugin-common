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
 * @copyright 2011-2012 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */
abstract class Controller
    extends BaseController
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

    protected function actionExportCSV()
    {
        $exporter = new ExportCSV();
        $exporter->export($this);
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
        if (!isset($action))
            $action = 'default';

        $method = 'action' . ucfirst($action);
        $this->$method();
    }

}
