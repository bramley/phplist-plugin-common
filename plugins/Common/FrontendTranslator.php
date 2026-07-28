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
    private $language;
    private $languageCode;
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
        $language = 'english';
        $require = "{$codeRoot}lan/frontend_english.php";

        if (preg_match('/(.+)\.inc$/', $languageFile, $matches)) {
            $f = "{$codeRoot}lan/frontend_{$matches[1]}.php";

            if (file_exists($f)) {
                $language = $matches[1];
                $require = $f;
            }
        }
        $this->language = $language;
        $this->languageCode = $this->languageToCode($language);
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

    /**
     * Convert a php date format to that used by IntlDateFormatter.
     *
     * @param string $phpFormat php date format
     *
     * @return string
     */
    public function convertFormat($phpFormat)
    {
        $intlFormats = [
            // Day
            'd' => 'd',     // 08
            'D' => 'eee',   // Mon
            'j' => 'd',     // 8
            'l' => 'EEEE',  // Monday
            'S' => '',
            // Month
            'F' => 'MMMM',  // July
            'm' => 'MM',    // 07
            'M' => 'MMM',   // Jul
            'n' => 'M',     // 7
            // Year
            'Y' => 'y',     // 2026
            'y' => 'yy',    // 26
        ];

        return strtr($phpFormat, $intlFormats);
    }

    public function languageCode()
    {
        return $this->languageCode;
    }

    /**
     * Convert a language name to the code used by IntlDateFormatter.
     *
     * @param string $language language name
     *
     * @return string
     */
    private function languageToCode($language)
    {
        $nameToCode = [
            'afrikaans' => 'af',
            'arabic' => 'ar',
            'belgianflemish' => '',
            'bosnian' => 'bs',
            'bulgarian' => 'bg',
            'catalan' => 'ca',
            'croatian' => 'hr',
            'czech' => 'cs',
            'danish' => 'da',
            'dutch' => 'nl',
            'english-gaelic' => 'en-GB',
            'english' => 'en-GB',
            'english-usa' => 'en',
            'estonian' => 'et',
            'finnish' => 'fi',
            'french' => 'fr',
            'german' => 'de',
            'greek' => 'el',
            'hebrew' => 'iw',
            'hungarian' => 'hu',
            'indonesian' => 'id',
            'italian' => 'it',
            'japanese' => 'ja',
            'latinamerican' => 'es',
            'norwegian' => 'no',
            'persian' => 'fa',
            'polish' => 'pl',
            'portuguese' => 'pt',
            'portuguese_pt' => 'pt-PT',
            'romanian' => 'ro',
            'russian' => 'ru',
            'serbian' => 'sr',
            'slovenian' => 'sl',
            'spanish' => 'es',
            'swedish' => 'sv',
            'swissgerman' => 'de-CH',
            'tchinese' => 'zh-TW',
            'turkish' => 'tr',
            'ukrainian' => 'uk',
            'usa' => 'en',
            'vietnamese' => 'vi',
        ];

        return isset($nameToCode[$language]) ? $nameToCode[$language] : '';
    }
}
