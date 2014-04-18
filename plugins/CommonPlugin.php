<?php
/**
 * CommonPlugin for phplist
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
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2013 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */


/**
 * Registers the plugin with phplist
 * 
 * @category  phplist
 * @package   CommonPlugin
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
    public $topMenuLinks = array(
        'phpinfo' => array('category' => 'config'),
        'config_file' => array('category' => 'config'),
    );
    public $pageTitles = array(
        'phpinfo' => 'Show phpinfo',
        'config_file' => 'Show config.php',
    );

    public function adminmenu()
    {
        return array();
    }

    public function __construct()
    {
        $this->coderoot = dirname(__FILE__) . '/CommonPlugin/';
        $this->version = (is_file($f = $this->coderoot . self::VERSION_FILE))
            ? file_get_contents($f)
            : '';
        parent::__construct();
        include_once $this->coderoot . 'functions.php';
    }
}
