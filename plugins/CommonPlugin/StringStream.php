<?php
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
class StringStream extends phpList\plugin\Common\StringStream
{
}

stream_wrapper_register('string', 'StringStream') or die('Failed to register string stream');
