<?php
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

namespace phpList\plugin\Common;

use DateInterval;
use DateTime;

/**
 * This class serves files.
 */
class FileServer
{
    private $expire = 604800;

    public function __construct($expire = null)
    {
        if ($expire) {
            $this->expire = $expire;
        }
    }

    /**
     * This method is used to send a file, such as an image or css, to the browser with appropriate http headers.
     *
     * @param string $filepath    path to the file
     * @param string $contentType content type to be used
     */
    public function serveFile($filepath, $contentType = null)
    {
        if (!file_exists($filepath)) {
            header('HTTP/1.0 404 Not Found');

            return;
        }
        $mtime = new DateTime('@' . filemtime($filepath));

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $since = new DateTime($_SERVER['HTTP_IF_MODIFIED_SINCE']);

            if ($mtime <= $since) {
                header('HTTP/1.1 304 Not Modified');

                return;
            }
        }
        $expiry = new DateTime();
        $expiry->add(new DateInterval('PT' . $this->expire . 'S'));
        header('Expires: ' . $expiry->format(DateTime::RFC1123));
        header("Cache-Control: max-age={$this->expire}");
        header('Last-Modified: ' . $mtime->format(DateTime::RFC1123));
        header('Pragma:');

        if (!$contentType) {
            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            $contentTypes = [
                'css' => 'text/css',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'bmp' => 'image/bmp',
            ];
            $contentType = isset($contentTypes[$ext]) ? $contentTypes[$ext] : 'application/octet-stream';
        }
        header("Content-type: $contentType");
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }
}
