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
 * Convenience class to create an HTML link to another page.
 */
class PageLink
{
    /*
     * Private variables
     */
    private $url;
    private $text;
    private $attrs;
    /*
     *    Public methods
     */

    /**
     * Constructor.
     *
     * @param string $url   the page url
     * @param string $text  text for link - this is not automatically html encoded
     * @param array  $attrs additional attributes for the A element
     */
    public function __construct($url, $text, array $attrs = array())
    {
        $this->url = $url;
        $this->text = $text;
        $this->attrs = $attrs;
    }

    /**
     * Generate a link for the given page and query parameters.
     *
     * @return string html <a> element
     */
    public function __toString()
    {
        $string = '';
        $this->attrs['href'] = $this->url;

        foreach ($this->attrs as $k => $v) {
            $string .= sprintf(' %s="%s"', $k, htmlspecialchars($v));
        }

        return sprintf('<a%s>%s</a>', $string, $this->text);
    }
}
