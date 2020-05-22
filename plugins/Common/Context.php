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
 * This class provides an abstraction to allow a page to write output when run from command line or in browser.
 */
class Context
{
    public static function create()
    {
        global $commandline, $inRemoteCall;

        return $commandline
        ? new CommandLineContext()
        : ($inRemoteCall
            ? new RemoteContext()
            : new BrowserContext()
        );
    }

    private function __construct()
    {
    }
}

class CommandLineContext extends Context
{
    public function start()
    {
        ob_end_clean();
    }

    public function finish()
    {
        echo "\n";
        ob_start();
    }

    public function output($line)
    {
        echo "\n", $line;
    }
}

class RemoteContext extends Context
{
    public function start()
    {
        ob_end_clean();
    }

    public function finish()
    {
        ob_start();
    }

    public function output($line)
    {
        echo $line, "\n";
    }
}

class BrowserContext extends Context
{
    public function start()
    {
        ob_end_flush();
    }

    public function finish()
    {
        ob_start();
    }

    public function output($line)
    {
        echo nl2br($line . "\n");
        flush();
    }
}
