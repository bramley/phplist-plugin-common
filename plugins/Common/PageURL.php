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
 * Convenience class to create a URL to either the current or another phplist page.
 */
class PageURL
{
    /*
     * Private variables
     */
    private $page;
    private $params;
    private $fragment;

    /**
     * Constructor.
     *
     * @param string $page     the page name
     * @param array  $params   additional parameters for the URL
     * @param string $fragment hash fragment to be appended to the URL
     */
    public function __construct($page = null, array $params = array(), $fragment = '')
    {
        $this->page = $page;
        $this->params = $params;
        $this->fragment = $fragment;
    }

    /**
     * Create a URL using the current parameters in $_GET.
     *
     * @param array  $params   parameters to override or add to those in $_GET
     * @param string $fragment hash fragment to be appended to the URL
     *
     * @return PageURL
     */
    public static function createFromGet(array $params = [], $fragment = '')
    {
        return new self(null, array_merge($_GET, $params), $fragment);
    }

    public function __toString()
    {
        global $installation_name;

        $p = array();

        if ($this->page) {
            $p['page'] = $this->page;
        } else {
            $p['page'] = $_GET['page'];
            $p['pi'] = $_GET['pi'];
        }
        $csrfName = $installation_name . '_csrf_token';

        if (isset($_SESSION[$csrfName])) {
            $p['tk'] = $_SESSION[$csrfName];
        }

        return './?' . http_build_query($p + $this->params, '', '&') . ($this->fragment ? "#$this->fragment" : '');
    }
}
