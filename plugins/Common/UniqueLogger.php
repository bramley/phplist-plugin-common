<?php
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
 * This class wraps a Logger in order to log a message only once within a session.
 */

namespace phpList\plugin\Common;

use Psr\Log\AbstractLogger;

class UniqueLogger extends AbstractLogger
{
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Extends the parent method by ensuring that a message is logged only once within a session.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        if (!isset($_SESSION[__CLASS__])) {
            $_SESSION[__CLASS__] = [];
        }
        $key = md5($message);

        if (!isset($_SESSION[__CLASS__][$key])) {
            $this->logger->log($level, $message, $context);
            $_SESSION[__CLASS__][$key] = $message;
        }
    }
}
