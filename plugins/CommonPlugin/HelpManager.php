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
 * @version   SVN: $Id: HelpManager.php 1014 2012-08-20 15:23:25Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class manages the display of help text
 */
class CommonPlugin_HelpManager
{
	const COMMON_PLUGIN = 'CommonPlugin';
	const HELP_TEMPLATE = '/helpmanager.tpl.php';
	const ABOUT_TEMPLATE = '/about.tpl.php';
	const VERSION_FILE = 'version.txt';
	const LICENCE_FILE = 'licence.txt';

	private $controller;
	private $plugin;
	private $pluginDir;

    private function pluginDir()
    {
        global $plugins;

		if (isset($_GET['pi'])) {
	        $this->plugin = preg_replace('/\W/', '', $_GET['pi']);

        	if (isset($plugins[$this->plugin]) && is_object($plugins[$this->plugin])) {
	            $this->pluginDir = $plugins[$this->plugin]->coderoot;
				return;
    	    }
		}
		throw new Exception('CommonPlugin_HelpManager must be created within a plugin');
    }

    private function about()
    {
		$params = array();
		$plugins = array();

		if (is_file($f = $this->pluginDir . self::VERSION_FILE)) {
			$version = file_get_contents($f);
			$plugins[] = array('name' => $this->plugin, 'version' => $version);
		}

		if (is_file($f = $this->pluginDir . self::LICENCE_FILE)
            ||
            is_file($f = PLUGIN_ROOTDIR . '/' . self::COMMON_PLUGIN . '/' . self::LICENCE_FILE)) {
			$params['pluginLicence'] = file_get_contents($f);
        }

		if (is_file($f = PLUGIN_ROOTDIR . '/' . self::COMMON_PLUGIN . '/' . self::VERSION_FILE)) {
			$version = file_get_contents($f);
			$plugins[] = array('name' => self::COMMON_PLUGIN, 'version' => $version);
		}

		$params['plugins'] = $plugins;
		return $this->controller->render(dirname(__FILE__) . self::ABOUT_TEMPLATE, $params);
   }

	private function configFile()
	{
        $r = "Charset: {$this->controller->i18n->charSet}<br/><br/>";

		if (isset($_SERVER['ConfigFile']) && is_file($f = $_SERVER['ConfigFile'])
			||
			is_file($f = '../config/config.php')
		) {
			$r .= 'Config file: ' . realpath($f);
			$regex = '/((?:user|password)\s*=\s*)(["\'])(.+?)\2/';
			$r .= '<pre>' . htmlspecialchars(preg_replace($regex, '$1$2* removed *$2', file_get_contents($f))) . '</pre>';
		} else {
			$r .= 'Cannot find config file';
		}
		return $r;
	}

	public function __construct($controller)
	{
		$this->controller = $controller;
	}

	public function display($topic)
	{
		ob_end_clean();

		if ($topic == 'phpinfo') {
			ob_start();
			try {
				phpinfo();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
			echo preg_replace('/(?<!&nbsp;)&nbsp;(?!&nbsp;)/', ' ', ob_get_clean());
			return;
		}

		$this->pluginDir();
		$params = array('topic' => $topic);

		if ($topic == 'config.php') {
			$params['help'] = $this->configFile();
		} elseif ($topic == 'about') {
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
		Header("Content-type: text/html; charset={$this->controller->i18n->charSet}");
		print $this->controller->render(dirname(__FILE__) . self::HELP_TEMPLATE, $params);
	}

    public static function version(phplistPlugin $plugin)
    {
		if (is_file($f = $plugin->coderoot . self::VERSION_FILE)) {
			return file_get_contents($f);
        }
    }
}
