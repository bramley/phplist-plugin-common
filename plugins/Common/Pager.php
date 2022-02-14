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
 * This class provides pagination of results.
 */
class Pager
{
    const START = 'start';
    const SHOW = 'show';

    /**
     * The number of instances of this class that have been created.
     *
     * @var int
     */
    private static $instances = 0;

    /**
     * URL query field names.
     *
     * @var int
     */
    private $start;
    private $show;

    /**
     * Item fields
     * total : the total number of items in the result set
     * startCurrent : the start item index for the current page, 0-based
     * startFinal : the start item index for the final page, 0-based.
     *
     * @var int
     */
    private $total;
    private $startCurrent;
    private $startFinal;

    /**
     * The maximum number of items to be displayed on the current page.
     *
     * @var int
     */
    private $pageSize;

    /**
     * The maximum number of items to be displayed on the current page for use in a URL.
     *
     * @var string
     */
    private $pageSizeStr;

    /**
     * List from which the user can select the number of items to display.
     *
     * @var array
     */
    private $itemsPerPage;

    /**
     * The default number of items to display when not specified in the URL
     * A value of null or 0 will display all items.
     *
     * @var int
     */
    private $defaultItems;

    /**
     * The previous and next links for the entity being displayed.
     *
     * @var string
     */
    private $linkPrev;
    private $linkNext;

    /**
     * Normalises an item index to be a multiple of $pageSize.
     *
     * @param int $i item index, 0-based
     *
     * @return int
     */
    private function normalise($i)
    {
        return intval($i / $this->pageSize) * $this->pageSize;
    }

    /**
     * Calculates the page size from the URL SHOW parameter or the default.
     */
    private function calculatePageSize()
    {
        if (isset($_GET[$this->show]) && strtolower($_GET[$this->show]) == 'all') {
            $this->pageSizeStr = 'All';
            $this->pageSize = $this->total;
        } elseif (isset($_GET[$this->show]) && ctype_digit($_GET[$this->show])) {
            $this->pageSize = $this->pageSizeStr = $_GET[$this->show];
        } elseif ($this->defaultItems) {
            $this->pageSize = $this->pageSizeStr = $this->defaultItems;
        } else {
            $this->pageSizeStr = 'All';
            $this->pageSize = $this->total;
        }
    }

    /**
     * Calculate the start item indices for the current page and the final page.
     */
    private function calculateStartItem()
    {
        if ($this->pageSize == 0) {
            $this->startCurrent = $this->startFinal = 0;

            return;
        }

        $this->startFinal = $this->normalise($this->total - 1);
        $this->startCurrent = min(
            $this->startFinal,
            isset($_GET[$this->start]) && ctype_digit($_GET[$this->start])
                ? $this->normalise($_GET[$this->start])
                : 0
        );
    }

    /**
     * Generate the html for a paging link.
     *
     * @param int    $start start item index
     * @param string $class css class
     * @param string $title title attribute
     *
     * @return string
     */
    private function pagingLink($start, $class, $title)
    {
        return new PageLink(
            PageURL::createFromGet([$this->start => $start], 'paging'),
            '',
            ['class' => $class, 'title' => $title]
        );
    }

    /**
     * Generate a link for the current page incorporating the $_GET parameters.
     *
     * @param string $text   text for link
     * @param array  $params additional parameters for the URL
     *
     * @return string html <a> element, html entity encoded
     */
    private function pageLink($text, array $params)
    {
        return new PageLink(PageURL::createFromGet($params), htmlspecialchars($text));
    }

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $suffix = self::$instances == 0 ? '' : self::$instances;
        ++self::$instances;
        $this->show = self::SHOW . $suffix;
        $this->start = self::START . $suffix;
        $this->setItemsPerPage(array(25, 50, 100), 50);
    }

    /**
     * Set the items per page array and default value
     * Adds 'All' as the final element of the array.
     *
     * @param array $itemsPerPage array of number of items per page
     * @param int   $default      default value
     */
    public function setItemsPerPage(array $itemsPerPage = array(), $default = null)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->itemsPerPage[] = 'All';
        $this->defaultItems = $default;
    }

    public function setPrevNext($param, $prev, $next)
    {
        $prevArrow = '&#x25c0;';
        $nextArrow = '&#x25b6;';
        $this->linkPrev = $prev
            ? new PageLink(
                PageURL::createFromGet(array($param => $prev, $this->start => 0)),
                $prevArrow
            )
            : $prevArrow;
        $this->linkNext = $next
            ? new PageLink(
                PageURL::createFromGet(array($param => $next, $this->start => 0)),
                $nextArrow
            )
            : $nextArrow;
    }

    /**
     * Returns the current item and the maximum number of items to be displayed.
     *
     * @return list (current index, number of items)
     */
    public function range()
    {
        return array($this->startCurrent, $this->pageSize);
    }

    /**
     * Generates the HTML for the pager using the pager template.
     *
     * @param int $total The total number of items
     *
     * @return string raw HTML
     */
    public function display($total)
    {
        $this->total = $total;
        $this->calculatePageSize();
        $this->calculateStartItem();

        $items = array();

        foreach ($this->itemsPerPage as $i) {
            $translatedPageSize = s($i);
            $items[] = $this->pageSizeStr == $i
                ? "<b>$translatedPageSize</b>"
                : $this->pageLink($translatedPageSize, array($this->start => $this->startCurrent, $this->show => $i));
        }
        $vars = [
            'range' => $this->total > 0
                ? s(
                    'Showing %d to %d of %d',
                    $this->startCurrent + 1,
                    min($this->startCurrent + $this->pageSize, $this->total),
                    $this->total
                )
                : '&nbsp;',
            'show' => '&nbsp;',
        ];

        if ($this->total > $this->pageSize) {
            if ($this->startCurrent == 0) {
                $vars['show'] = s('Show') . ' ' . implode(' | ', $items);
            }
            $vars += [
                'first' => $this->pagingLink(0, 'first', s('First Page')),
                'back' => $this->pagingLink(max(0, $this->startCurrent - $this->pageSize), 'previous', s('Previous')),
                'forward' => $this->pagingLink(min($this->startFinal, $this->startCurrent + $this->pageSize), 'next', s('Next')),
                'last' => $this->pagingLink($this->startFinal, 'last', s('Last Page')),
            ];
        }

        if (isset($this->linkPrev)) {
            $vars['prev'] = $this->linkPrev;
            $vars['next'] = $this->linkNext;
        }

        return new View(__DIR__ . '/pager.tpl.php', $vars);
    }
}
