# Common Plugin #

## Description ##
This plugin provides support classes required by my other plugins.
It also provides three pages which are added to the Config menu

* display the output of the phpinfo() function
* display the config.php file (user id and passwords are removed)
* display the contents of the php session

When installing or upgrading a plugin ensure that the latest CommonPlugin is installed as well.

## Installation ##

### Dependencies ###

This plugin is for phplist 3.0.0 and later.

Requires php version 5.3 or later.

The version of this plugin dated 2015-03-23 has an incompatible change. 
Other plugins that depend on CommonPlugin should be upgraded to their latest version.

### Set the plugin directory ###
The default plugin directory is `plugins` within the phplist `admin` directory but you can use a directory outside
of the web root by changing the definition of `PLUGIN_ROOTDIR` in config.php.
The benefit of this is that installed plugins will not be affected when you upgrade phplist.

### Install through phplist ###
The recommended way to install is through the Plugins page (menu Config > Manage plugins) using the package URL `https://github.com/bramley/phplist-plugin-common/archive/master.zip`.

In phplist releases 3.0.5 and earlier there is a bug that can cause a plugin to be incompletely installed on some configurations (<https://mantis.phplist.com/view.php?id=16865>). 
Check that these files are in the plugin directory. If not then you will need to install manually. The bug has been fixed in release 3.0.6.

* the file CommonPlugin.php
* the directory CommonPlugin
* the directory Common

Then click the small orange icon to enable the plugin.

### Install manually ###
Download the plugin zip file from <https://github.com/bramley/phplist-plugin-common/archive/master.zip>

Expand the zip file, then copy the contents of the plugins directory to your phplist plugins directory.
This should contain

* the file CommonPlugin.php
* the directory CommonPlugin
* the directory Common

## Version history ##

    version     Description
    3.5.2+20160217  Use same tab style as core phplist
    3.5.1+20160204  Improve German translation
    3.5.0+20160110  Improve display of email addresses
                    Remove Emogrifier package
    3.4.0+20151213  Update Emogrifier package
    3.3.0+20151124  Update picoFeed and KLogger packages
    3.2.0+20151023  Internal changes
    3.1.1+20151015  Minor internal changes
    3.1.0+20151012  csrf token compatibility change for phplist 3.2.1
    3.0.4+20151007  Remove need for plugins directory to be called plugins
    3.0.3+20150828  Refactoring pager and listing classes
    3.0.2+20150819  Make image page a public page
    3.0.1+20150813  Include tk parameter on page urls
    3.0.0           Added namespaced classes
                    new version numbering
    2015-03-29      Improved German translation
    2015-03-28      Internal changes for translation
    2015-03-23      Change to autoload approach
    2015-03-22      Add Picofeed package
    2015-02-13      Use composer to install packages
    2015-01-27      Use latest KLogger package
    2015-01-06      Minor change
    2014-10-22      Minor changes
    2014-09-09      Internal changes
    2014-09-04      Minor changes
    2014-07-06      Minor changes
    2014-05-18      Accumulated internal changes
    2014-04-22      Syntax hightlight config file
    2014-04-18      Add phpinfo and config file as separate pages
    2014-04-08      Internal changes
    2014-03-11      Replace check-box by drop-down list on attribute form
    2014-02-16      Internal change
    2014-02-12      Hold config settings for each admin separately
    2014-02-03      Improve presentation by always displaying the listing
    2014-01-27      Added page to serve plugin images
    2014-01-25      Added class to support google charts
    2013-11-05      Display attribute form in a UIPanel
    2013-10-03      Accumulated changes
    2013-08-26      Accumulated changes
    2013-04-30      Internal changes to work with phplist 2.11.8
    2013-04-22      Fix for GitHub issue, internal changes
    2013-03-29      Initial version for phplist 2.11.x releases
