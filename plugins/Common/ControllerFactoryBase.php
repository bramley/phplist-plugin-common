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
 * Abstract factory class providing default methods for creating a controller.
 *
 * @category  phplist
 */
abstract class ControllerFactoryBase
{
    protected $defaultType = null;

    /**
     * Helper method to create a controller using plugin and type.
     *
     * @param string $pi     the plugin
     * @param array  $params further parameters from the URL
     *
     * @return Controller
     */
    protected function createControllerType($pi, array $params)
    {
        $type = isset($params['type']) ? $params['type'] : $this->defaultType;
        $class = $pi . '_Controller_' . ucfirst($type);

        return new $class();
    }

    /**
     * Default implementation to create a controller using plugin only, type is ignored
     * Must be over-ridden by a sub-class if type needs to be used.
     *
     * @param string $pi     the plugin
     * @param array  $params further parameters from the URL
     *
     * @return Controller
     */
    public function createController($pi, array $params)
    {
        $class = 'phpList\plugin\\' . $pi . '\Controller';

        return new $class();
    }
}
