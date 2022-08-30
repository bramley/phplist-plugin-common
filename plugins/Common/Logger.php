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
use Katzgrau\KLogger;
use Psr\Log\NullLogger;

class Logger extends KLogger\Logger
{
    protected static $instance;
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

        if (isset(static::$instance)) {
            return static::$instance;
        }

        if (isset($log_options['threshold']) && defined('Psr\Log\LogLevel::' . $log_options['threshold'])) {
            $threshold = constant('Psr\Log\LogLevel::' . $log_options['threshold']);
            $dir = isset($log_options['dir']) ? $log_options['dir'] : $tmpdir;
            $logger = new static($dir, $threshold);
            $logger->setDateFormat('D d M Y H:i:s');
        } else {
            $logger = new NullLogger();
        }
        static::$instance = $logger;

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
        parent::__construct($dir, $threshold);
    }

    /**
     * Overrides the parent method.
     * Logs messages only from configured classes.
     * Prepends the calling class/method/line number to the message.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->logLevels[$this->logLevelThreshold] < $this->logLevels[$level]) {
            return;
        }
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        /*
         * [0] is AbstractLogger calling this method
         * [1] is the caller of debug(), info() etc, which gives the line number
         * [2] is the previous level, which gives the class/method of the caller of debug(), info() etc
         *
         * The frame index is increased by 1 when log() is implemented by a wrapper or subclass
         * [0] is a wrapper or subclass calling this method
         * [1] is AbstractLogger calling the log() method of a wrapper or subclass
         * [2] is the caller of debug(), info() etc, which gives the line number
         * [3] is the previous level, which gives the class/method of the caller of debug(), info() etc
         */
        foreach ([1, 2] as $i) {
            $caller = $trace[$i];
            $previous = $trace[$i + 1];

            if (isset($previous['class']) && isset($this->classes[$previous['class']])) {
                if ($this->classes[$previous['class']]) {
                    $logMessage = sprintf(
                        "%s::%s, line %d\n%s",
                        $previous['class'],
                        $previous['function'],
                        $caller['line'],
                        (string) $message
                    );
                    $this->write($this->formatMessage($level, $logMessage, $context));
                }

                return;
            }
        }
    }
}
