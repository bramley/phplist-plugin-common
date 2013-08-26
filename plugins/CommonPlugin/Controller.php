<?php
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

/**
 * This is the base class for Controller.
 * It provides the common functionality shared by controllers that need to render views. 
 * 
 */
abstract class CommonPlugin_BaseController
{
    /*
     *    Public attributes
     */
    public $i18n;
    public $logger;
    /*
     *    Public methods
     */

    public function __construct()
    {
        $this->i18n = CommonPlugin_I18N::instance();
        $this->logger = CommonPlugin_Logger::instance();
    }

    public function render($_template, array $_params = array())
    {
        /*
         * Capture the rendering of the template
         */
        extract($_params);
        ob_start();
        try {
            include $_template;
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
    }
}
/**
 * This class manages the running of a controller using the action parameter
 */
abstract class CommonPlugin_Controller
    extends CommonPlugin_BaseController
{
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
            $help = new CommonPlugin_HelpManager($this);
            $help->display($_GET['topic']);
        }
        exit;
    }

    protected function actionChart()
    {
        if (isset($_GET['chartID'])) {
            $chart = new CommonPlugin_GoogleChart();
            $chart->sendChart($_GET['chartID']);
        }
        exit;
    }

    protected function actionExport()
    {
        $exporter = new CommonPlugin_ExportCSV();
        $exporter->export($this);
        exit;
    }

    protected function actionImage()
    {
        $loader = new CommonPlugin_ImageLoader();
        $loader->load($_GET['image']);
        exit;
    }

    protected function normalise(&$post)
    {
        array_walk_recursive($post,    array($this, 'stripSlashes'));
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
    }

    public function run($action = null)
    {
        if (!isset($action))
            $action = 'default';

        $method = 'action' . ucfirst($action);
        $this->$method();
    }

}
