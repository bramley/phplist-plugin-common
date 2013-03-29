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
 * @version   SVN: $Id: ControllerFactoryBase.php 683 2012-03-20 17:30:58Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Abstract factory class providing default methods for creating a controller
 * 
 * @category  phplist
 * @package   CommonPlugin
 */
abstract class CommonPlugin_ControllerFactoryBase
{
	protected $defaultType = null;

    /**
     * Helper method to create a controller using plugin and type
     * @param string $pi the plugin
     * @param array $params further parameters from the URL
     * @return CommonPlugin_Controller 
     * @access protected
     */
    protected function createControllerType($pi, array $params)
    {
		$type = isset($params['type']) ? $params['type'] : $this->defaultType;
		$class = $pi . '_Controller_' . ucfirst($type);
		return new $class();
	}

    /**
     * Default implementation to create a controller using plugin only, type is ignored
	 * Must be over-ridden by a sub-class if type needs to be used
     * @param string $pi the plugin
     * @param array $params further parameters from the URL
     * @return CommonPlugin_Controller 
     * @access public
     */
    public function createController($pi, array $params)
    {
		$class = $pi . '_Controller';
		return new $class();
	}
}
