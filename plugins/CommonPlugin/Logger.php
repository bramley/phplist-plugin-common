<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2012 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 * @version   SVN: $Id: Logger.php 505 2012-01-01 18:33:47Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class extends KLogger to provide configuration through config.php entries.
 * It over-rides the log() method to include the calling class/method/line number
 * 
 */ 
class CommonPlugin_Logger extends KLogger
{
	private static $instance;
	private $threshold;
	private $classes;
	/*
	 *	Public methods
	 */

    /**
     * Replaces the instance() method in KLogger by keeping its own singleton
	 * Creates a configured instance using entries from config.php
     *
     * @param string  $logDirectory File path to the logging directory
     * @param integer $severity     One of the pre-defined severity constants
     * @return CommonPlugin_Logger
     */
    static public function instance($logDirectory = false, $severity = false)
	{
		global $log_options;
		global $tmpdir;

		if (isset(self::$instance))
			return self::$instance;

		if ($logDirectory) {
			$dir = $logDirectory;
		} elseif (isset($log_options['dir'])) {
			$dir = $log_options['dir'];
		} elseif (isset($tmpdir)) {
			$dir = $tmpdir;
		} else {
			$dir = '/var/tmp';
		}

		if ($severity) {
			$threshold = $severity;
		} elseif (isset($log_options['level']) && defined("KLogger::{$log_options['level']}")) {
			$threshold = constant("KLogger::{$log_options['level']}");
		} else {
			$threshold = KLogger::OFF;
		}

		if (isset($_GET['pi'])) {
			$pi = preg_replace('/\W/', '', $_GET['pi']);
			$dir .= '/' . $pi;
		}
		self::setDateFormat('D d M Y H:i:s');
		self::$instance = new self($dir, $threshold);
        return self::$instance;
	}

	public function __construct($dir, $threshold)
	{
		global $log_options;

		$this->classes = isset($log_options['classes']) ? $log_options['classes'] : array();
		$this->threshold = $threshold;
		parent::__construct($dir, $threshold);
	}

	public function log($message, $level)
	{
		if ($this->threshold == KLogger::OFF || $level > $this->threshold)
			return;

		$trace = debug_backtrace(false);

		if (!empty($this->classes[$trace[1]['class']])) {
			$i = 1;
		} elseif (!empty($this->classes[$trace[2]['class']])) {
			$i = 2;
		} else {
			return;
		}

		$message = 
			"{$trace[$i]['class']}::{$trace[$i]['function']}, line {$trace[$i - 1]['line']} "
			. $message;
		parent::log($message, $level);
 	}


}