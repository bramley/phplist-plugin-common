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
 * Class to customise the display of \JasonGrimes\Paginator.
 */
class Paginator extends \JasonGrimes\Paginator
{
    /**
     * Render an HTML pagination control.
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->numPages <= 1) {
            return '';
        }
        $html = '<div class="pagination">';
        $html .= $this->getPrevUrl()
            ? sprintf('<a class="unselected" href="%s">%s</a>', htmlspecialchars($this->getPrevUrl()), $this->previousText)
            : sprintf('<span class="inactive">%s</span>', $this->previousText);

        foreach ($this->getPages() as $page) {
            $class = $page['isCurrent'] ? 'selected' : 'unselected';
            $html .= $page['url']
                ? sprintf('<a class="%s" href="%s">%d</a>', $class, htmlspecialchars($page['url']), $page['num'])
                : sprintf('<span class="inactive">%s</span>', $page['num']);
        }
        $html .= $this->getNextUrl()
            ? sprintf('<a class="unselected" href="%s">%s</a>', htmlspecialchars($this->getNextUrl()), $this->nextText)
            : sprintf('<span class="inactive">%s</span>', $this->nextText);
        $html .= '</div>';
        $html .= "\n";

        return $html;
    }
}
