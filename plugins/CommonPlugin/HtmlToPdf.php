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
 * @version   SVN: $Id: HtmlToPdf.php 1234 2013-03-17 15:42:12Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */


/**
 * This class extends WkHtmlToPdf to provide some convenience methods
 * 
 */
class CommonPlugin_HtmlToPdf extends WkHtmlToPdf
{
    public function headerHtml($html)
    {
        $this->options['header-html'] = $this->createTmpFile($html);
    }
    
    public function footerHtml($html)
    {
        $this->options['footer-html'] = $this->createTmpFile($html);
    }

}
