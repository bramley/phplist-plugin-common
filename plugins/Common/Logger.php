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
 * @copyright 2011-2023 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/*
 * This class wraps KLogger to provide configuration through config.php entries.
 * It implements the log() method to include the calling class name.
 */
use Katzgrau\KLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\NullLogger;

class Logger implements LoggerInterface
{
    use LoggerTrait;

    private static $instance;
    private $classes;
    private $wrappedLogger;

    /**
     * Creates an instance of this class wrapping KLogger\Logger.
     * Returns NullLogger when logging is disabled.
     *
     * @return Psr\Log\LoggerInterface
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
            $dir = $log_options['dir'] ?? $tmpdir;
            $klogger = new \Katzgrau\KLogger\Logger($dir, $threshold);
            $klogger->setDateFormat('H:i:s');
            $logger = new static($klogger);
        } else {
            $logger = new NullLogger();
        }
        static::$instance = $logger;

        return static::$instance;
    }

    /**
     * @param Psr\Log\LoggerInterface $logger wrapped logger
     */
    public function __construct($logger)
    {
        global $log_options;

        $this->wrappedLogger = $logger;
        $this->classes = $log_options['classes'] ?? array();
    }

    /**
     * Logs messages only from configured classes and class prefixes.
     * Prepends the calling class to the message.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        /*
         * [0] is LoggerTrait calling this method from the debug method
         * [1] is the caller of debug(), info() etc
         * [2] is the previous level, which gives the class/method of the caller of debug(), info() etc
         *
         * The frame index is increased by 1 when log() is implemented by a wrapper or subclass
         * [0] is a wrapper or subclass calling this method
         * [1] is LoggerTrait calling the log() method of a wrapper or subclass
         * [2] is the caller of debug(), info() etc
         * [3] is the previous level, which gives the class/method of the caller of debug(), info() etc
         */
        foreach ([1, 2] as $i) {
            $caller = $trace[$i];
            $previous = $trace[$i + 1];

            if (isset($previous['class'])) {
                $class = $previous['class'];

                if (isset($this->classes[$class])) {
                    $key = $class;
                    $found = true;
                } else {
                    $parts = explode('\\', $class, 2);
                    $key = $parts[0];
                    $found = isset($this->classes[$key]);
                }

                if ($found) {
                    if ($this->classes[$key]) {
                        $shortClass = str_replace('phpList\plugin\\', '', $previous['class']);
                        $logMessage = sprintf('%s %s', $shortClass, (string) $message);
                        $this->wrappedLogger->log($level, $logMessage, $context);
                    }

                    return;
                }
            }
        }
    }
}
