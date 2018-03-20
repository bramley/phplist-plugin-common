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
 * This class provides translation of text strings
 * The same approach as core phplist is used. A language file for each translated language.
 * If a language file does not exist then the class falls-back to English.
 * The language files are in the lan subdirectory of a plugin's main directory.
 */
class I18N
{
    /*
     *    Private attributes
     */
    private $coreI18N;
    private $iconv;
    private $lan;

    private static $instance;
    /*
     *    Public attributes
     */
    public $charSet;

    /**
     * Private constructor for the singleton pattern.
     *
     * @param phplistPlugin $pi an optional plugin whose language file should
     *                          be used for translations
     *
     * @throws Exception if the code is not executing within a plugin
     *
     * @return string the plugin's directory
     */
    public function __construct(\phplistPlugin $pi = null)
    {
        global $I18N, $strCharSet, $plugins;

        $this->charSet = strtoupper($strCharSet);
        $this->coreI18N = $I18N;
        $this->iconv = function_exists('iconv');

        $pluginDir = $pi ? $pi->coderoot : $this->pluginDir();
        $this->lan = $this->loadLanguageFile($this->languageDir($pluginDir));
        $this->lan += $this->loadLanguageFile($this->languageDir($plugins['CommonPlugin']->coderoot));
    }

    /**
     * Derives the directory for a plugin using the $_GET parameter.
     *
     * @throws Exception if the code is not executing within a plugin
     *
     * @return string the plugin's directory
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
        throw new \Exception('I18N must be created within a plugin');
    }

    /**
     * Derives the language directory within a plugin directory.
     *
     * @param string $pluginDir the plugin directory
     *
     * @return string the actual or best guess language directory
     */
    private function languageDir($pluginDir)
    {
        if (is_dir($pluginDir.'lan/')) {
            $dir = $pluginDir.'lan/';
        } else {
            $dir = $pluginDir;
        }

        return $dir;
    }

    /**
     * Searches for the language file beneath a given directory.
     *
     * @param string $dir the target directory
     *
     * @return array array of translations
     */
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

    /**
     * Returns the single instance of this class.
     *
     * @return I18N The instance of this class
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }

        return self::$instance;
    }

    /**
     * Translates a key or array of keys.
     * Tries lower case when the key does not exist.
     * Further parameters can be provided for sprintf() type formatting.
     *
     * @param array|string $key   the key or keys to be translated
     * @param mixed        $v,... additional variables to format with vsprintf()
     *
     * @return array|string a translated string if one is found, or the key if not found
     */
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

    /**
     * Translates a key or array of keys in UTF-8.
     *
     * @param array|string $key the key or keys to be translated
     *
     * @return array|string a translated string if one is found, or the key if not found
     */
    public function getUtf8($key)
    {
        if ($this->charSet == 'UTF-8') {
            return $this->get($key);
        }

        if (is_array($key)) {
            return array_map(array($this, 'getUtf8'), $key);
        }

        $t = $this->get($key);

        return $this->iconv
            ? iconv($this->charSet, 'UTF-8', $t)
            : utf8_encode($t);
    }
}
