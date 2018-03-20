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

/**
 * Add the plugin directories to the composer autoload
 * Use PSR-4 for namespaced plugins
 * Use PSR-0 for non-namespaced plugins.
 *
 * Create autoloader for other plugins.
 * Add classmap for plugins.
 */
function CommonPlugin_Autoloader_main()
{
    global $systemroot, $plugins;

    $loader = require dirname(__FILE__) . '/vendor/autoload.php';

    $paths = (PLUGIN_ROOTDIRS == '') ? array() : explode(';', PLUGIN_ROOTDIRS);
    $paths[] = (CommonPlugin_Autoloader_isAbsolutePath(PLUGIN_ROOTDIR))
        ? PLUGIN_ROOTDIR : $systemroot . '/' . PLUGIN_ROOTDIR;

    $loader->addPsr4('phpList\\plugin\\', $paths);
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
