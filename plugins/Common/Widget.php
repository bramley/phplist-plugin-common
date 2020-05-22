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

use UIPanel;

/**
 * This class provides customised links and html.
 */
class Widget
{
    const ATTRIBUTEFORM_TEMPLATE = '/widget_attributeform.tpl.php';

    private function __construct()
    {
    }

    public static function attributeForm(BaseController $controller, $model, $search = true, $select = true)
    {
        $params = array(
            'model' => $model,
            'showSearch' => $search,
            'showSelect' => $select,
        );
        $title = $search ? s('Find subscribers') : s('Select attributes');
        $panel = new UIPanel(
            $title,
            new View(__DIR__ . self::ATTRIBUTEFORM_TEMPLATE, $params)
        );

        return $panel->display();
    }
}
