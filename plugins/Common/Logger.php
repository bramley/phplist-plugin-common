<?php

namespace phpList\plugin\Common;

/*
 * CommonPlugin for phplist
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/*
 * This class extends KLogger to provide configuration through config.php entries.
 * It over-rides the log() method to include the calling class/method/line number
 *
 */
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
     * Creates a configured instance using entries from config.php.
     *
     * @return Psr\Log\AbstractLogger
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
            $dir = isset($log_options['dir']) ? $log_options['dir'] : $tmpdir;
            $logger = new self($dir, $threshold);
            $logger->setDateFormat('D d M Y H:i:s');
        } else {
            $logger = new NullLogger();
        }
        self::$instance = $logger;

        return $logger;
    }

    /**
     * Constructor.
     *
     * @param string $dir       File path to the logging directory
     * @param string $threshold One of the pre-defined PSR severity constants
     */
    public function __construct($dir, $threshold)
    {
        global $log_options;

        $this->classes = isset($log_options['classes']) ? $log_options['classes'] : array();
        $this->threshold = $threshold;
        parent::__construct($dir, $threshold);
    }

    /**
     * Logs messages only from configured classes.
     * Prepends the calling class/method/line number to the message.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $trace = debug_backtrace(false, 3);
        /*
         * [0] is AbstractLogger calling this method
         * [1] is the caller of debug(), info() etc, which gives the line number
         * [2] is the previous level, which gives the class/method of the caller
         */
        $frame = 1;

        if (empty($this->classes[$trace[$frame + 1]['class']])) {
            return;
        }
        $message = sprintf(
            "%s::%s, line %d\n",
            $trace[$frame + 1]['class'],
            $trace[$frame + 1]['function'],
            $trace[$frame]['line']
        ) . $message;
        parent::log($level, $message, $context);
    }
}
