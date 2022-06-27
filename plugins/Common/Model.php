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
 * This is a base class providing common functionality for all models.
 */
abstract class Model
{
    /*
     *    Private variables
     */
    private $config;
    /*
     *    Protected variables
     */
    protected $properties = array();
    protected $persist = array();

    /*
     *    Protected methods
     */
    protected function __construct($configItem = null)
    {
        if ($configItem) {
            $admin = $_SESSION['logindetails']['id'];
            $item = "$configItem|$admin";
            $this->config = new Config($item);
            $properties = $this->config->get('properties');

            if (!is_null($properties)) {
                $this->properties = array_merge($this->properties, $properties);
            }
        }
    }

    /*
     *    Public methods
     */
    public function setProperties(array $new)
    {
        $dirty = false;

        foreach ($this->properties as $key => &$value) {
            if (isset($new[$key])) {
                $v = $new[$key];
                /*
                 * unselected check-boxes come as zero values in a numerically indexed array
                 * remove and re-index array
                 */
                if (is_array($v) && isset($v[0])) {
                    $value = array_values(array_filter($v));
                } else {
                    $value = $v;
                }
                $dirty = $dirty || isset($this->persist[$key]);
            }
        }
        unset($value);

        if ($dirty) {
            $p = array_intersect_key($this->properties, $this->persist);
            $this->config->set('properties', $p);
        }
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function __get($name)
    {
        return $this->properties[$name];
    }

    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }
}
