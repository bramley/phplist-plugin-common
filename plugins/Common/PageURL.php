<?php

namespace phpList\plugin\Common;

/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2014 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Convenience class to create a URL to either the current or another phplist page
 * 
 */
class PageURL
{
    /*
     * Private variables
     */
    private $page;
    private $params;
    private $fragment;
    /*
     *    Public methods
     */
    /**
     * Constructor
     * @param string $page the page name
     * @param array $params additional parameters for the URL
     * @param string $fragment hash fragment to be appended to the URL
     * @access public
     */
    public function __construct($page = null, array $params = array(), $fragment = '')
    {
        $this->page = $page;
        $this->params = $params;
        $this->fragment = $fragment;
    }

    public function __toString()
    {
        $p = array();

        if ($this->page) {
            $p['page'] = $this->page;
        } else {
            $p['page'] = $_GET['page'];
            $p['pi'] = $_GET['pi'];
        }

        return './?' . http_build_query($p + $this->params, '', '&') . ($this->fragment ? "#$this->fragment" : '');
    }
}
