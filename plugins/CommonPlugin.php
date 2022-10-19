<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */
use Pelago\Emogrifier\CssInliner;

/**
 * Registers the plugin with phplist.
 */
class CommonPlugin extends phplistPlugin
{
    const VERSION_FILE = 'version.txt';
    const CSS_INLINE_NONE = 'None';
    const CSS_INLINE_PREMAILER = 'PreMailer';
    const CSS_INLINE_EMOGRIFIER = 'Emogrifier';

    /*
     *  Inherited variables
     */
    public $name = 'Common Plugin';
    public $enabled = true;
    public $authors = 'Duncan Cameron';
    public $description = 'Provides support classes required by some other plugins.';
    public $documentationUrl = 'https://resources.phplist.com/plugin/common';
    public $priority = 100;
    public $topMenuLinks = array(
        'phpinfo' => array('category' => 'config'),
        'config_file' => array('category' => 'config'),
        'session' => array('category' => 'config'),
    );
    public $publicPages = array('image');

    public function __construct()
    {
        $this->coderoot = dirname(__FILE__) . '/' . __CLASS__ . '/';
        $this->settings = [
            'common_inline_css_package' => [
                'description' => 'The package to use to inline CSS',
                'type' => 'select',
                'value' => self::CSS_INLINE_NONE,
                'values' => array(
                    self::CSS_INLINE_NONE => self::CSS_INLINE_NONE,
                    self::CSS_INLINE_EMOGRIFIER => self::CSS_INLINE_EMOGRIFIER,
                    self::CSS_INLINE_PREMAILER => self::CSS_INLINE_PREMAILER,
                ),
                'allowempty' => false,
                'category' => 'campaign',
            ],
            'common_pdf_logo_path' => [
                'value' => '',
                'description' => 'File system path or URL to a logo to be used on PDF reports',
                'type' => 'text',
                'allowempty' => true,
                'category' => 'campaign',
            ],
        ];

        parent::__construct();
        $this->version = (is_file($f = $this->coderoot . self::VERSION_FILE))
            ? file_get_contents($f)
            : '';
        include_once $this->coderoot . 'functions.php';
        include_once $this->coderoot . 'polyfill.php';
    }

    public function activate()
    {
        require $this->coderoot . 'Autoloader.php';

        $this->pageTitles = array(
            'phpinfo' => s('phpinfo'),
            'config_file' => s('config.php'),
            'session' => s('php session'),
        );
        parent::activate();
    }

    public function adminmenu()
    {
        return array();
    }

    public function dependencyCheck()
    {
        return array(
            'PHP version 5.6.0 or greater' => version_compare(PHP_VERSION, '5.6') > 0,
            'phpList version 3.3.2 or later' => version_compare(VERSION, '3.3.2') >= 0,
        );
    }

    /**
     * Hook called on logout.
     * Use this to update plugin translations for the current language.
     */
    public function logout()
    {
        global $I18N, $plugins, $tables;

        foreach ($plugins as $piName => $pi) {
            $languageFile = sprintf('%slan/translations_%s.php', $pi->coderoot, $I18N->language);

            if (!file_exists($languageFile)) {
                continue;
            }
            $configKey = sprintf('%s_translations_%s', $piName, $I18N->language);
            $lastUpdate = getConfig($configKey);
            $modified = filemtime($languageFile);

            if ($lastUpdate == '' || $lastUpdate < $modified) {
                $changed = false;
                $translations = require $languageFile;

                foreach ($translations as $t) {
                    /*
                     * Add translation if it does not already exist
                     */
                    $original = sql_escape($t[0]);
                    $translation = sql_escape($t[1]);
                    $query = <<<END
                        INSERT IGNORE INTO {$tables['i18n']}
                        (lan, original, translation)
                        VALUES ('$I18N->language', '$original', '$translation')
END;
                    Sql_Query($query);

                    if (Sql_Affected_Rows() > 0) {
                        $changed = true;
                    } else {
                        /*
                         * Update translation if it has changed
                         */
                        $query = <<<END
                            UPDATE {$tables['i18n']}
                            SET translation = '$translation'
                            WHERE lan = '$I18N->language' AND original = '$original' AND translation != '$translation'
END;
                        Sql_Query($query);
                        $changed = $changed || Sql_Affected_Rows() > 0;
                    }
                }

                if ($changed) {
                    logEvent("Translations updated for $piName language $I18N->language");
                }
                SaveConfig($configKey, $modified);
            }
        }
    }

    /**
     * Use this hook to inline CSS in the final email body.
     *
     * @param PHPMailer $mail instance of PHPMailer
     *
     * @return array
     */
    public function messageHeaders($mail)
    {
        if ($mail->ContentType != 'text/html') {
            return [];
        }
        $package = getConfig('common_inline_css_package');

        if ($package == self::CSS_INLINE_PREMAILER) {
            $preMailer = new \Crossjoin\PreMailer\HtmlString($mail->Body);
            $preMailer->setOption($preMailer::OPTION_HTML_COMMENTS, $preMailer::OPTION_HTML_COMMENTS_KEEP);
            $preMailer->setOption($preMailer::OPTION_CSS_WRITER_CLASS, '\Crossjoin\Css\Writer\Pretty');
            $inlinedHtml = $preMailer->getHtml();
            $mail->Body = $inlinedHtml;
        } elseif ($package == self::CSS_INLINE_EMOGRIFIER && version_compare(PHP_VERSION, '7.2') >= 0) {
            $inlinedHtml = CssInliner::fromHtml($mail->Body)->inlineCss()->render();
            $mail->Body = $inlinedHtml;
        }

        return [];
    }
}
