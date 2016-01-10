<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2015 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

    /**
     * Add the plugin directories to the composer autoload
     * Use PSR-4 for namespaced plugins
     * Use PSR-0 for non-namespaced plugins and classes in the ext directory
     *
     * @access  public
     * @return  void
     */

function CommonPlugin_Autoloader_main()
{
    global $systemroot, $plugins;

    $loader = require dirname(__FILE__) . '/vendor/autoload.php';

    $paths = (PLUGIN_ROOTDIRS == '') ? array() : explode(';', PLUGIN_ROOTDIRS);
    $paths[] = (CommonPlugin_Autoloader_isAbsolutePath(PLUGIN_ROOTDIR))
        ? PLUGIN_ROOTDIR : $systemroot . '/' . PLUGIN_ROOTDIR;

    $loader->addPsr4('phpList\\plugin\\', $paths);
    $iterator = new DirectoryIterator(dirname(__FILE__) . '/ext');

    foreach ($iterator as $file) {
        if ($file->isDir() && !$file->isDot()) {
            $paths[] = $file->getPathname();
        }
    }
    $loader->add('', $paths);

    foreach ($plugins as $piName => $pi) {
        if ($piName != 'CommonPlugin' && file_exists($ownAutoloader = $pi->coderoot . 'vendor/autoload.php')) {
            require $ownAutoloader;
        }

        if (file_exists($f = $pi->coderoot . 'class_map.php')) {
            $base = dirname($pi->coderoot);
            $piClassMap = include $f;
            $loader->addClassMap($piClassMap);
        }
    }
}

function CommonPlugin_Autoloader_isAbsolutePath($path)
{
    return preg_match('@^(?:/|\\\\|\w:\\\\|\w:/)@', $path);
}

CommonPlugin_Autoloader_main();
