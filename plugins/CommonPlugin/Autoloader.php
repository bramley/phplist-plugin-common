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
 */


/**
 * Convenience function to create and register the class loader
 * 
 */
include dirname(__FILE__) . '/ClassLoader.php';

function CommonPlugin_Autoloader_main()
{
    global $systemroot;

    $loader = new CommonPlugin_ClassLoader();

    if (PLUGIN_ROOTDIRS != '') {
        foreach (explode(';',PLUGIN_ROOTDIRS) as $dir) {
            $loader->addBasePath($dir);
        }
    }

    if (CommonPlugin_Autoloader_isAbsolutePath(PLUGIN_ROOTDIR)) {
        $pluginDir = PLUGIN_ROOTDIR;
    } else {
        $pluginDir = $systemroot . '/' . PLUGIN_ROOTDIR;
    }
    $loader->addBasePath($pluginDir);

    $iterator = new DirectoryIterator(dirname(__FILE__) . '/ext');
    
    foreach ($iterator as $file) {
        if ($file->isDir() && !$file->isDot()) {
            $loader->addBasePath($file->getPathname());
        }
    }
    $loader->register();
}

function CommonPlugin_Autoloader_isAbsolutePath($path)
{
    return preg_match('@^(?:/|\\\\|\w:\\\\|\w:/)@', $path);
}

CommonPlugin_Autoloader_main();
