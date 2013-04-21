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
 * This class provides configuration settings
 */

class CommonPlugin_Config
{
    private $dao;
    private $id;
    private $config;

    public function __construct($id, $default = array())
    {
        $this->id = $id;
        $this->dao = new CommonPlugin_DAO_Config(new CommonPlugin_DB());
        $this->config = unserialize($this->dao->getItem($id));

        if ($this->config === FALSE) {
            $this->config = $default;
        }
    }

    public function get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;
        $r = $this->dao->setItem($this->id, serialize($this->config));
    }
}

