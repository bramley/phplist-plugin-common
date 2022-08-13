<?php

namespace phpList\plugin\Common;

use function phpList\plugin\Common\shortenText as shortenText;

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
 * This class overrides some methods of the WebblerListing class.
 */
class WebblerListing extends \WebblerListing
{
    /*
     * Work-around for Trevelyn theme to stop links being displayed as buttons.
     * Make the webblerlisting table responsive.
     */
    public function __construct($title = '', $help = '')
    {
        global $pagefooter;

        parent::__construct($title, $help);
        $pagefooter[basename(__FILE__)] = <<<'END'
<script>
$(document).ready(function(){
    $('a.nobutton').removeClass('btn btn-xs btn-primary');
    $('div.responsive-listing .content').first().addClass('table-responsive');

});
</script>
END;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Extend parent method to wrap the listing in a div element to make the webblerlisting table responsive.
     */
    public function display($add_index = 0, $class = '')
    {
        return sprintf('<div class="responsive-listing">%s</div>', parent::display($add_index, $class));
    }

    /**
     * Extend parent method to convert url to html entities.
     */
    public function addElement($element, $url = '', $colsize = '')
    {
        parent::addElement($element, htmlspecialchars($url), $colsize);
        parent::setClass($element, 'row1');
    }

    /**
     * Extend parent method.
     * Construct the link here in order to be able to specify attributes and fallback to 'nobutton' class.
     */
    public function addColumn($name, $column_name, $value, $url = '', $align = '', array $attributes = [])
    {
        $columnValue = $url ? $this->createLink($url, $value, $value, $attributes) : htmlspecialchars($value, ENT_QUOTES);
        parent::addColumn($name, $column_name, $columnValue, '', $align);
    }

    /**
     * Extend parent method to convert value and url to html entities.
     */
    public function addRow($name, $row_name, $value, $url = '', $align = '', $class = '')
    {
        parent::addRow($name, $row_name, nl2br(htmlspecialchars($value, ENT_QUOTES)), htmlspecialchars($url), $align, $class);
    }

    /**
     * Convenience method to shorten an email address when used as the value.
     */
    public function addColumnEmail($name, $column_name, $value, $url = '', $align = '')
    {
        $shortValue = shortenText($value);
        $columnValue = $url
            ? $this->createLink($url, $shortValue, $value)
            : htmlspecialchars($shortValue, ENT_QUOTES);
        parent::addColumn($name, $column_name, $columnValue, '', $align);
    }

    /**
     * Convenience method when the value is already valid html.
     */
    public function addColumnHtml($name, $column_name, $value, $url = '', $align = '')
    {
        parent::addColumn($name, $column_name, $value, htmlspecialchars($url), $align);
    }

    /**
     * Convenience method when the value is already valid html.
     */
    public function addRowHtml($name, $row_name, $value, $url = '', $align = '', $class = '')
    {
        parent::addRow($name, $row_name, $value, htmlspecialchars($url), $align, $class = '');
    }

    /**
     * Create a link with attributes adding nobutton class and title.
     *
     * @param string $url        value for the href attribute
     * @param string $value      the link value treated as text
     * @param string $title      value for the title attribute
     * @param array  $attributes attributes for the link
     *
     * @return an html a element
     */
    private function createLink($url, $value, $title, array $attributes = [])
    {
        $additionalAttributes = ['class' => 'nobutton', 'title' => $title];

        return new PageLink($url, htmlspecialchars($value, ENT_QUOTES), $attributes + $additionalAttributes);
    }
}
