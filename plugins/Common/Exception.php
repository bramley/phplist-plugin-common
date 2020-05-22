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
 * This is a base exception from which all other exceptions inherit.
 */
abstract class Exception extends \Exception
{
    protected $i18n;

    /*
     *    Public methods
     */
    public function __construct($message = '', $code = 0)
    {
        $i18n = I18N::instance();
        $args = func_get_args();

        if (func_num_args() > 1) {
            unset($args[1]);
        }
        $t = call_user_func_array(array($i18n, 'get'), $args);
        parent::__construct($t, $code);
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!($errno & error_reporting())) {
            return true;
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
