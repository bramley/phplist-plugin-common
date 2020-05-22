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
 * This class provides configuration settings.
 */
class Config
{
    private $dao;
    private $id;
    private $config;

    public function __construct($id, $default = array())
    {
        $this->id = $id;
        $this->dao = new DAO\Config(new DB());
        $this->config = unserialize($this->dao->getItem($id));

        if ($this->config === false) {
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
