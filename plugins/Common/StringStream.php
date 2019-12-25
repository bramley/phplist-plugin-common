<?php

namespace phpList\plugin\Common;

/**
 * String Stream Wrapper.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2012-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class allows a PHP variable to be used as a read/write stream.
 *
 * Based on code originally developed by Sam Moffatt <sam.moffatt@toowoombarc.qld.gov.au>
 * See http://code.google.com/p/phpstringstream/
 */
class StringStream
{
    const MODE_READ = 1;
    const MODE_WRITE = 2;

    private $_currentstring;
    private $_mode;
    private $_pos;

    private static $references = array();

    public static function stringId(&$variable)
    {
        $id = uniqid();
        self::$references[$id] = &$variable;

        return $id;
    }

    public static function fopen(&$variable, $mode = 'r')
    {
        $id = self::stringId($variable);
        $fh = fopen("string://$id", $mode);

        return $fh;
    }

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $id = parse_url($path, PHP_URL_HOST);

        if (!isset(self::$references[$id])) {
            return false;
        }

        $this->_currentstring = &self::$references[$id];

        if (strpos($mode, 'r') !== false) {
            $this->_mode = self::MODE_READ;
        } elseif (strpos($mode, 'w') !== false) {
            $this->_currentstring = '';
            $this->_mode = self::MODE_WRITE;
        } else {
            return false;
        }
        $this->_pos = 0;

        return true;
    }

    public function stream_stat()
    {
        return false;
    }

    public function stream_read($count)
    {
        if ($this->_mode != self::MODE_READ) {
            return false;
        }

        $result = substr($this->_currentstring, $this->_pos, $count);
        $this->_pos += strlen($result);

        return $result;
    }

    public function stream_write($data)
    {
        if ($this->_mode != self::MODE_WRITE) {
            return false;
        }

        $count = strlen($data);
        $this->_currentstring = substr_replace($this->_currentstring, $data, $this->_pos, 0);
        $this->_pos += $count;

        return $count;
    }

    public function stream_tell()
    {
        return $this->_pos;
    }

    public function stream_eof()
    {
        return $this->_pos >= strlen($this->_currentstring);
    }

    /*     public function stream_seek($offset, $whence)
        {
            echo "\n$offset $whence $this->_pos $this->_len ";
            switch ($whence) {
                case SEEK_SET:
                    if (!($offset >= 0 && $offset < $this->_len))
                        return false;
                    $this->_pos = $offset;
                    break;
                case SEEK_CUR:
                    if (!($offset >= 0 && $this->_pos + $offset < $this->_len))
                        return false;
                    $this->_pos += $offset;
                    break;
                case SEEK_END:
                    if (!($offset <= 0 && $offset + $this->_len >= 0))
                        return false;
                    $this->_pos = $this->_len + $offset;
                    break;
            }
            return true;
        } */
}

stream_wrapper_register('string', 'phpList\plugin\Common\StringStream') or die('Failed to register string stream');
