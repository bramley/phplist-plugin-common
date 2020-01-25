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
 * This class provides a common method to create and run a controller.
 */
class Main
{
    /**
     * Create and run a controller.
     *
     * @param ControllerFactoryBase $cf     a controller factory
     * @param string                $action the controller action
     */
    public static function run(ControllerFactoryBase $cf = null, $action = '')
    {
        $errorsHandled = E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT;
        $level = error_reporting($errorsHandled);
        set_error_handler('phpList\plugin\Common\Exception::errorHandler', $errorsHandled);

        try {
            if (!$cf) {
                $cf = new ControllerFactory();
            }
            $controller = $cf->createController($_GET['pi'], $_GET);
            $action = $action ?: (isset($_GET['action']) ? $_GET['action'] : null);
            $controller->run($action);
        } catch (\Exception $e) {
            echo '<p>' . nl2br(htmlspecialchars($e->getMessage())) . '</p>';
            echo '<p>' . nl2br(htmlspecialchars($e->getTraceAsString())) . '</p>';
        }
        restore_error_handler();
        error_reporting($level);
    }
}
