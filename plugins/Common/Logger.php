<?php

namespace phpList\plugin\Common;

/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2017 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class extends KLogger to provide configuration through config.php entries.
 * It over-rides the log() method to include the calling class/method/line number
 * 
 */
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Katzgrau\KLogger;

class Logger extends KLogger\Logger
{
    private static $instance;
    private $threshold;
    private $classes;

    /*
     *    Public methods
     */

    /**
     * Creates a configured instance using entries from config.php
     *
     * @param string  $logDirectory File path to the logging directory
     * @param string $severity     One of the pre-defined PSR severity constants
     * @return Logger
     */
    public static function instance()
    {
        global $log_options;
        global $tmpdir;

        if (isset(self::$instance)) {
            return self::$instance;
        }

        if (isset($log_options['threshold']) && defined('Psr\Log\LogLevel::' . $log_options['threshold'])) {
            $threshold = constant('Psr\Log\LogLevel::' . $log_options['threshold']);

            if (isset($log_options['dir'])) {
                $dir = $log_options['dir'];
            } elseif (isset($tmpdir)) {
                $dir = $tmpdir;
            } else {
                $dir = '/var/tmp';
            }

            if (isset($_GET['pi'])) {
                $pi = preg_replace('/\W/', '', $_GET['pi']);
                $dir .= '/' . $pi;
            }
            $logger = new self($dir, $threshold);
            $logger->setDateFormat('D d M Y H:i:s');
        } else {
            $logger = new NullLogger();
        }

        self::$instance = $logger;
        return $logger;
    }

    public function __construct($dir, $threshold)
    {
        global $log_options;

        $this->classes = isset($log_options['classes']) ? $log_options['classes'] : array();
        $this->threshold = $threshold;
        parent::__construct($dir, $threshold);
    }

    public function log($level, $message, array $context = array())
    {
        $trace = debug_backtrace(false);

        if (!empty($this->classes[$trace[1]['class']])) {
            $i = 1;
        } elseif (!empty($this->classes[$trace[2]['class']])) {
            $i = 2;
        } elseif (!empty($this->classes[$trace[3]['class']])) {
            $i = 3;
        } else {
            return;
        }

        $message =
            "{$trace[$i]['class']}::{$trace[$i]['function']}, line {$trace[$i - 1]['line']}\n"
            . $message;
        parent::log($level, $message, $context);
    }

    public function isDebug()
    {
        return $this->threshold == LogLevel::DEBUG;
    }
}
