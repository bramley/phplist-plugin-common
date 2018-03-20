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
 * This is the base class for Controller.
 * It provides the common functionality shared by controllers that need to render views.
 */
abstract class BaseController
{
    /*
     *    Public methods
     */

    public function __construct()
    {
    }

    public function render($_template, array $_params = array())
    {
        /*
         * Capture the rendering of the template
         */
        extract($_params);
        ob_start();
        try {
            include $_template;
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean();
    }
}
