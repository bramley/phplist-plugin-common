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
 * This class provides translation of text strings
 * The same approach as core phplist is used. A language file for each translated language.
 * If a language file does not exist then the class falls-back to English.
 * The language files are in the lan subdirectory of a plugin's main directory.
 * 
 */ 
 class CommonPlugin_I18N 
{
    /*
     *    Private attributes
     */
    private $coreI18N;
    private $iconv;
    private $lan = array();

    private static $instance;
    /*
     *    Public attributes
     */
    public $charSet;

    /*
     *    Private methods
     */
    private function pluginDir()
    {
        global $plugins;

        if (isset($_GET['pi'])) {
            $pi = preg_replace('/\W/', '', $_GET['pi']);

            if (isset($plugins[$pi]) && is_object($plugins[$pi])) {
                return $plugins[$pi]->coderoot;
            }
        }
        return null;
    }

    private function languageDir($pluginDir)
    {
        if (is_dir($pluginDir . 'lan/')) {
            $dir = $pluginDir . 'lan/';
        } else {
            $dir = $pluginDir;
        }
        return $dir;
    }

    private function loadLanguageFile($dir)
    {
        if (is_file($f = "{$dir}{$this->coreI18N->language}_{$this->charSet}.php")
            ||
            is_file($f = "{$dir}{$this->coreI18N->language}.php")
            ||
            is_file($f = "{$dir}en.php")
        ) {
            @include $f;
        } else {
            $lan = array();
        }
        return $lan;
    }
    /*
     *    Public methods
     */
    public function __construct(phplistPlugin $pi = null)
    {
        global $I18N, $strCharSet;

        $this->charSet = strtoupper($strCharSet);
        $this->coreI18N = $I18N;
        $this->iconv = function_exists('iconv');

        $pluginDir = $pi ? $pi->coderoot : $this->pluginDir();

        if ($pluginDir) {
            $this->lan = $this->loadLanguageFile($this->languageDir($pluginDir));
        }
        $this->lan += $this->loadLanguageFile($this->languageDir(dirname(__FILE__) . '/'));
    }

    public static function instance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public function get($key)
    {
        if (is_array($key)) {
            return array_map(array($this, 'get'), $key);
        }

        if (isset($this->lan[$key])) {
            $t = $this->lan[$key];
        } elseif (isset($this->lan[strtolower($key)])) {
            $t = $this->lan[strtolower($key)];
        } else {
            $t = $key;
        }

        if (func_num_args() > 1) {
            $args = func_get_args();
            array_shift($args);
            $t = vsprintf($t, $args);
        }
        return $t;
    }

    public function getUtf8($key)
    {
        if ($this->charSet == 'UTF-8')
            return $this->get($key);

        if (is_array($key))
            return array_map(array($this, 'getUtf8'), $key);

        $t = $this->get($key);
        return $this->iconv
            ? iconv($this->charSet, 'UTF-8', $t)
            : utf8_encode($t);
    }
}
