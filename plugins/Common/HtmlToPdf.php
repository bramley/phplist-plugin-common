<?php

namespace phpList\plugin\Common;

use WkHtmlToPdf;

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
 * This class extends WkHtmlToPdf to provide some convenience methods.
 */
class HtmlToPdf extends WkHtmlToPdf
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
