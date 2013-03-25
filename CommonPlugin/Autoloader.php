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
 * @version   SVN: $Id: Autoloader.php 1234 2013-03-17 15:42:12Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */


/**
 * Convenience function to create and register the class loader
 * 
 */
include dirname(__FILE__) . '/ClassLoader.php';

function CommonPlugin_Autoloader_main()
{
	$loader = new CommonPlugin_ClassLoader();

    foreach (explode(';',PLUGIN_ROOTDIRS) as $dir) {
        $loader->addBasePath($dir);
    }

    $iterator = new DirectoryIterator(PLUGIN_ROOTDIR . '/CommonPlugin/ext');
    
    foreach ($iterator as $file) {
        if ($file->isDir()) {
            $loader->addBasePath($file->getPathname());
        }
    }
	$loader->register();
}
CommonPlugin_Autoloader_main();
