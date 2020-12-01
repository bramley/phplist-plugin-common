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

/**
 * This class provides translations for plugin text on frontend pages.
 */
class FrontendTranslator
{
    private $translations;

    /**
     * Load the frontend file for the subscribe page language or, if not set, the default frontend language.
     *
     * @param array  $pageData subscribe page fields
     * @param string $codeRoot path to plugin's code directory
     */
    public function __construct($pageData, $codeRoot)
    {
        global $language_module;

        $languageFile = !empty($pageData['language_file'])
            ? $pageData['language_file']
            : $language_module;
        $require = "{$codeRoot}lan/frontend_english.php";

        if (preg_match('/(.+)\.inc$/', $languageFile, $matches)) {
            $language = $matches[1];
            $f = "{$codeRoot}lan/frontend_$language.php";

            if (file_exists($f)) {
                $require = $f;
            }
        }
        $this->translations = require $require;
    }

    /**
     * @param string $key     text to be translated
     * @param mixed  ...$args optional sprintf parameters
     *
     * @return string
     */
    public function s($key, ...$args)
    {
        $translated = isset($this->translations[$key]) ? $this->translations[$key] : $key;

        return count($args) > 0
             ? sprintf($translated, ...$args)
             : $translated;
    }
}
