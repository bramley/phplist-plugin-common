<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2013 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Convenience class to create an HTML link to another page
 * 
 */
class CommonPlugin_PageLink
{
    /*
     *    Public methods
     */
    public function __construct($page, $text, array $params, array $attrs = array())
    {
        $this->page = $page;
        $this->text = $text;
        $this->params = $params;
        $this->attrs = $attrs;
    }

    /**
     * Generate a link for the given page and query parameters
     * @param string $page the page name
     * @param string $text text for link - this is not automatically html encoded
     * @param array $params additional parameters for the URL
     * @return string html <a> element
     * @access private
     */
    public function __toString()
    {
        $string = '';
        $this->attrs['href'] = new CommonPlugin_PageURL($this->page, $this->params);

        foreach ($this->attrs as $k => $v) {
            $v = htmlspecialchars($v);
            $string .= " $k=\"$v\"";
        }
		return sprintf('<a%s>%s</a>', $string, $this->text);
    }
}
