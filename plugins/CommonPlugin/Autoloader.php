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
 * Convenience function to create and register the class loader
 * 
 */

function CommonPlugin_Autoloader_main()
{
    global $systemroot;

    $loader = require dirname(__FILE__) . '/vendor/autoload.php';

    $paths = (PLUGIN_ROOTDIRS == '') ? array() : explode(';',PLUGIN_ROOTDIRS);
    $paths[] = (CommonPlugin_Autoloader_isAbsolutePath(PLUGIN_ROOTDIR))
        ? PLUGIN_ROOTDIR : $systemroot . '/' . PLUGIN_ROOTDIR;
    $loader->add('', $paths);
    $loader->addPsr4('phpList\\plugin\\', $paths);
}

function CommonPlugin_Autoloader_isAbsolutePath($path)
{
    return preg_match('@^(?:/|\\\\|\w:\\\\|\w:/)@', $path);
}

CommonPlugin_Autoloader_main();
