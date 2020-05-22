<?php

namespace phpList\plugin\Common;

use WebblerTabs;

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
 * This class extends the WebblerTabs class.
 */
class Tabs extends WebblerTabs
{
    public function display()
    {
        return '<div class="minitabs">'
            . parent::display()
            . '</div>';
    }

    public function addTab($caption, $url = '', $name = '')
    {
        parent::addTab(htmlspecialchars($caption), htmlspecialchars($url), $name);
    }
}
