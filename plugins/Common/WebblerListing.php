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
 * @copyright 2011-2017 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class overrides some methods of the WebblerListing class
 * 
 */
class WebblerListing extends \WebblerListing
{
    public function __construct($title = '', $help = '')
    {
        parent::__construct($title, $help);
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /*
     *    Override parent methods to convert value and url to html entities
     */
    public function addElement($element, $url = '', $colsize = '')
    {
        parent::addElement($element, htmlspecialchars($url), $colsize);
        parent::setClass($element, 'row1');
    }

    public function addColumn($name, $column_name, $value, $url = '', $align = '')
    {
        parent::addColumn($name, $column_name, htmlspecialchars($value, ENT_QUOTES), htmlspecialchars($url), $align);
    }

    public function addRow($name, $row_name, $value, $url = '', $align = '', $class = '')
    {
        parent::addRow($name, $row_name, nl2br(htmlspecialchars($value, ENT_QUOTES)), htmlspecialchars($url), $align, $class);
    }

    /*
     *    Additional convenience methods
     */
    public function addColumnEmail($name, $column_name, $value, $url = '', $align = '')
    {
        $maxLength = 29;

        if (strlen($value) > $maxLength) {
            $middle = '…';
            $outerLength = (int)(($maxLength - strlen($middle)) / 2);
            $shortValue = sprintf(
                '<span title="%s">%s</span>',
                $value,
                htmlspecialchars(substr($value, 0, $outerLength)) . $middle . htmlspecialchars(substr($value, -$outerLength))
            );
        } else {
            $shortValue = htmlspecialchars($value);
        }
        $this->addColumnHtml($name, $column_name, $shortValue, $url, $align);
    }

    public function addColumnHtml($name, $column_name, $value, $url = '', $align = '')
    {
        parent::addColumn($name, $column_name, $value, htmlspecialchars($url), $align);
    }

    public function addRowHtml($name, $row_name, $value, $url = '', $align = '', $class = '')
    {
        parent::addRow($name, $row_name, $value, htmlspecialchars($url), $align, $class = '');
    }
}
