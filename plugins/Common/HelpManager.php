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
 * This class manages the display of help text.
 */
class HelpManager
{
    const COMMON_PLUGIN = 'CommonPlugin';
    const HELP_TEMPLATE = '/helpmanager.tpl.php';
    const ABOUT_TEMPLATE = '/about.tpl.php';
    const VERSION_FILE = 'version.txt';
    const LICENCE_FILE = 'licence.txt';

    private $controller;
    private $plugin;
    private $pluginDir;

    private function findPlugin()
    {
        global $plugins;

        if (isset($_GET['pi'])) {
            $piName = preg_replace('/\W/', '', $_GET['pi']);

            if (isset($plugins[$piName])) {
                $this->plugin = $plugins[$piName];
                $this->pluginDir = $this->plugin->coderoot;

                return;
            }
        }
        throw new \Exception('HelpManager must be created within a plugin');
    }

    private function about()
    {
        global $plugins;

        $commonPi = $plugins[self::COMMON_PLUGIN];
        $params = array();
        $params['plugins'] = array(
            array('name' => $this->plugin->name, 'version' => $this->plugin->version),
            array('name' => $commonPi->name, 'version' => $commonPi->version),
        );

        if (is_file($f = $this->pluginDir . self::LICENCE_FILE)
            ||
            is_file($f = $commonPi->coderoot . self::LICENCE_FILE)) {
            $params['pluginLicence'] = file_get_contents($f);
        }

        return $this->controller->render(__DIR__ . self::ABOUT_TEMPLATE, $params);
    }

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->findPlugin();
    }

    public function display($topic)
    {
        ob_end_clean();

        $params = array('topic' => $topic);

        if ($topic == 'about') {
            $params['help'] = $this->about();
        } else {
            $lang = $_SESSION['adminlanguage']['iso'];

            if (is_file($f = "{$this->pluginDir}help/$lang/$topic.php")
                ||
                is_file($f = "{$this->pluginDir}help/en/$topic.php")
            ) {
                $params['file'] = $f;
            } else {
                $params['help'] = 'No help available';
            }
        }
        header("Content-type: text/html; charset={$this->controller->i18n->charSet}");
        echo $this->controller->render(__DIR__ . self::HELP_TEMPLATE, $params);
    }
}
