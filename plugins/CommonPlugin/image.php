<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2014 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 *  This page serves plugin images
 *
 */

function CommonPlugin_ServeImage($image)
{
    $defaultExpire = 604800;
    $filepath = dirname(__FILE__) . '/images/' . basename($image);

    if (!file_exists($filepath)) {
        header('HTTP/1.0 404 Not Found');
        return;
    }
    $mtime = new DateTime('@' . filemtime($filepath));
    $expiry = new DateTime();
    $expiry->add(new DateInterval('PT' . $defaultExpire . 'S'));
    header('Expires: ' . $expiry->format(DateTime::RFC1123));
    header("Cache-Control: max-age=$defaultExpire");
    header('Last-Modified: ' . $mtime->format(DateTime::RFC1123));
    header('Pragma:');

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $since = new DateTime($_SERVER['HTTP_IF_MODIFIED_SINCE']);

        if ($mtime <= $since) {
            header('HTTP/1.1 304 Not Modified');
            return;
        }
    }

    $ext = pathinfo($filepath, PATHINFO_EXTENSION);
    $cTypes = array(
        'png' => 'png',
        'gif' => 'gif',
        'jpg' => 'jpeg',
        'jpeg' => 'jpeg',
        'bmp' => 'bmp'
    );
    $type = isset($cTypes[$ext]) ? $cTypes[$ext] : 'jpeg';
    header("Content-type: image/$type");
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
}

ob_end_clean();
CommonPlugin_ServeImage($_GET['image']);
exit;
