<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2013 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 * @version   SVN: $Id: ExportCSV.php 717 2012-03-30 19:33:23Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class manages the export in CSV format
 * 
 */
class CommonPlugin_ImageLoader
{
	public function __construct()
	{
	}

	public function load($image) 
	{
		ob_end_clean();
        $filepath = PLUGIN_ROOTDIR . '/CommonPlugin/images/' . basename($image);

        if (!file_exists($filepath)) {
            header('HTTP/1.0 404 Not Found');
            return;
        }
        $mtime = date('r', filemtime($filepath));

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $dt = new DateTime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            $since = $dt->getTimestamp();
            
            if ($mtime < $since) {
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
        // header mime type
        header('Content-Length: ' . filesize($filepath));
        header("Last-Modified: $mtime");
        header('Pragma:');
        readfile($filepath);
	}
}
