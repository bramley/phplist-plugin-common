# Common Plugin #

## Description ##
This plugin provides support classes required by my other plugins.
When installing or upgrading a plugin ensure that the latest CommonPlugin is installed as well.

## Installation ##

### Dependencies ###

This plugin is for phplist 3.0.0 and later.

Requires php version 5.2 or later.

### Set the plugin directory ###
You can use a directory outside of the web root by changing the definition of `PLUGIN_ROOTDIR` in config.php.
The benefit of this is that plugins will not be affected when you upgrade phplist.

### Install through phplist ###
The recommended way to install is through the Plugins page (menu Config > Plugins) using the package URL `https://github.com/bramley/phplist-plugin-common/archive/master.zip`.

### Install manually ###
Download the plugin zip file from <https://github.com/bramley/phplist-plugin-common/archive/master.zip>

Expand the zip file, then copy the contents of the plugins directory to your phplist plugins directory.
This should contain

* the file CommonPlugin.php
* the directory CommonPlugin

## Version history ##

    version     Description
    2014-01-25  Added class to support google charts
    2013-11-05  Display attribute form in a UIPanel
    2013-10-03  Accumulated changes
    2013-08-26  Accumulated changes
    2013-04-30  Internal changes to work with phplist 2.11.8
    2013-04-22  Fix for GitHub issue, internal changes
    2013-03-29  Initial version for phplist 2.11.x releases
