<?php

namespace phpList\plugin\Common;

use UIPanel;

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
 * This class provides customised links and html.
 */
class Widget
{
    const ATTRIBUTEFORM_TEMPLATE = '/widget_attributeform.tpl.php';

    /*
     *    Private methods
     */
    private function __construct()
    {
    }

    /*
     *    Public methods
     */
    public static function attributeForm(BaseController $controller, $model, $search = true, $select = true)
    {
        $params = array(
            'model' => $model,
            'showSearch' => $search,
            'showSelect' => $select,
        );
        $title = $search ? $controller->i18n->get('Find subscribers') : $controller->i18n->get('Select attributes');
        $panel = new UIPanel($title,
            $controller->render(dirname(__FILE__) . self::ATTRIBUTEFORM_TEMPLATE, $params)
        );

        return $panel->display();
    }
}
