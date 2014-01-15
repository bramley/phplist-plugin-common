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
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class provides a common method to create and run a controller
 * 
 */
class CommonPlugin_Main
{
    const REQUIRED_VERSION = '5.2.0';

    public static function run(CommonPlugin_ControllerFactoryBase $cf = null)
    {
        $level = error_reporting(E_ALL | E_STRICT);
        set_error_handler('CommonPlugin_Exception::errorHandler', E_ALL | E_STRICT);

        try {
            $version = phpversion();

            if (version_compare($version, self::REQUIRED_VERSION) < 0) 
                throw new Exception(sprintf("php version $version found, plugin requires version %s or later", self::REQUIRED_VERSION));

            if (!$cf)
                $cf = new CommonPlugin_ControllerFactory();

            $controller = $cf->createController($_GET['pi'], $_GET);
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $controller->run($action);
        } catch (Exception $e) {
            print '<p>' . nl2br(htmlspecialchars($e->getTraceAsString ())) . '</p>';
            print '<p>' . nl2br(htmlspecialchars($e->getMessage())) . '</p>';
        }
        restore_error_handler();
        error_reporting($level);
    }
}
