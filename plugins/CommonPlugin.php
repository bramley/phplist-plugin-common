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
use phpList\plugin\Common\Logger;

/**
 * Registers the plugin with phplist.
 */
class CommonPlugin extends phplistPlugin
{
    const VERSION_FILE = 'version.txt';

    /*
     *  Inherited variables
     */
    public $name = 'Common Plugin';
    public $enabled = true;
    public $authors = 'Duncan Cameron';
    public $description = 'Provides support classes required by some other plugins.';
    public $documentationUrl = 'https://resources.phplist.com/plugin/common';
    public $topMenuLinks = array(
        'phpinfo' => array('category' => 'config'),
        'config_file' => array('category' => 'config'),
        'session' => array('category' => 'config'),
    );
    public $publicPages = array('image');

    public function __construct()
    {
        $this->coderoot = dirname(__FILE__) . '/CommonPlugin/';
        parent::__construct();
        $this->version = (is_file($f = $this->coderoot . self::VERSION_FILE))
            ? file_get_contents($f)
            : '';
        include_once $this->coderoot . 'functions.php';
    }

    public function sendFormats()
    {
        require_once $this->coderoot . 'Autoloader.php';
        $i18n = new CommonPlugin_I18N($this);
        $this->pageTitles = array(
            'phpinfo' => $i18n->get('view_phpinfo'),
            'config_file' => $i18n->get('view_config.php'),
            'session' => $i18n->get('view_session'),
        );

        return null;
    }

    public function adminmenu()
    {
        return array();
    }

    public function dependencyCheck()
    {
        global $database_module;

        return array(
            'PHP version 5.4.0 or greater' => version_compare(PHP_VERSION, '5.4') > 0,
            'phpList must use mysqli (not mysql)' => $database_module == 'mysqli.inc',
        );
    }

    /**
     * Hook called on logout.
     * Use this to update plugin translations.
     */
    public function logout()
    {
        global $plugins, $tables;

        $logger = Logger::instance();

        foreach ($plugins as $piName => $pi) {
            if (!file_exists($langDir = $pi->coderoot . 'lan')) {
                continue;
            }

            foreach (new DirectoryIterator($langDir) as $file) {
                if ($file->isDir()) {
                    continue;
                }

                if (!preg_match('/^translations_(.+)\.php$/', $file->getFilename(), $matches)) {
                    $logger->debug('Ignoring ' . $file->getPathname());
                    continue;
                }
                $language = $matches[1];
                $configKey = sprintf('%s_translations_%s', $piName, $language);
                $lastUpdate = getConfig($configKey);
                $modified = $file->getMTime();

                if (!($lastUpdate == '' || $lastUpdate < $modified)) {
                    $logger->debug("Not changed last update $lastUpdate modified $modified" . $file->getPathname());
                    continue;
                }
                $translations = require $file->getPathname();

                foreach ($translations as $t) {
                    $original = sql_escape($t[0]);
                    $translation = sql_escape($t[1]);
                    $query = <<<END
                        REPLACE INTO {$tables['i18n']}
                        (lan, original, translation)
                        VALUES ('$language', '$original', '$translation')
END;
                    Sql_Query($query);
                }
                SaveConfig($configKey, $modified);
                logEvent("Translations updated for $piName language $language");
            }
        }
    }
}
