# Common Plugin #

## Description ##
This plugin provides support classes required by my other plugins.
It also provides three pages which are added to the Config menu

* display the output of the phpinfo() function
* display the config.php file (user id and passwords are removed)
* display the contents of the php session

The plugin is now included in phplist so you should not normally need to install the plugin yourself.
However when installing or upgrading another plugin ensure that you upgrade to the latest CommonPlugin as well.

## Installation ##

### Dependencies ###

This plugin requires phplist 3.3.2 or later.

Requires php version 5.6 or later.

### Install through phplist ###
The recommended way to install is through the Plugins page (menu Config > Manage plugins) using the package URL `https://github.com/bramley/phplist-plugin-common/archive/master.zip`.

### Install manually ###
Download the plugin zip file from <https://github.com/bramley/phplist-plugin-common/archive/master.zip>

Expand the zip file, then copy the contents of the plugins directory to your phplist plugins directory.
This should contain

* the file CommonPlugin.php
* the directory CommonPlugin
* the directory Common

## Version history ##

    version     Description
    3.15.5+20210428 Allow the plugin to be a dependency of phplist. Fixes #10.
    3.15.4+20210418 Minor change to the MailSender class
    3.15.3+20210410 Minor internal changes
    3.15.2+20210305 Slight optimisation of checking for language updates
    3.15.1+20210218 Minor change to translations
    3.15.0+20210207 Add FPDF package
    3.14.3+20210201 Avoid invalid tag errors in the phpinfo page
    3.14.2+20201201 Support sprintf parameters in front-end translations
    3.14.1+20201110 Remove export event log page, now in Addons Plugin
    3.14.0+20201107 Add page to export the event log
    3.13.2+20201102 Add method to update the template content
    3.13.1+20200606 Include premailer as a choice for inlining CSS
    3.13.0+20200602 Inline CSS using Emogrifier
    3.12.2+20200517 Minor internal changes
    3.12.1+20200415 Improve timing of database queries
    3.12.0+20200412 Mail client support for Amazon SES signing the message body
    3.11.2+20200314 Add Dutch translations thanks to by Peter Buijs.
    3.11.1+20200310 Add functions to split a string into lines and to get a config entry as lines.
    3.11.0+20200306 Internal changes
    3.10.13+20200216 Make the webblerlisting table responsive
    3.10.12+20200127 Minor internal change
    3.10.11+20200125 Minor internal change
    3.10.10+20191225 Minor improvements to StringStream class
    3.10.9+20191024 Minor change to display of the config.php file
    3.10.8+20190914 Improve display of phpinfo with the trevelin theme
    3.10.7+20190902 Add DAO for template table
    3.10.6+20190825 Remove unnecessary output of command line signature
    3.10.5+20190730 Support clicking a chart to redirect to another page
    3.10.4+20190609 Use the same paging controls as core phplist
    3.10.3+20190528 Improve display of listing and pager
    3.10.2+20190509 Improve display of icons
    3.10.1+20190318 Log event for update of frontend translations only when they have changed
    3.10.0+20190222 Improvements to translations, other internal changes
    3.9.8+20190129  Minor change to MailSender class
    3.9.7+20181230  Internal changes
    3.9.6+20181210  Minor internal changes
    3.9.5+20181203  Add Russian translation file
    3.9.4+20181130  Bug fix for MailSender class
    3.9.3+20181116  Minor internal changes
    3.9.2+20181022  Internal changes
    3.9.1+20181020  Internal changes
    3.9.0+20180905  Collection of internal changes
    3.8.2+20180730  Minor bug fix
    3.8.1+20180729  Add method to delete 'not sent' rows from usermessage table
    3.8.0+20180621  Add dependency on phplist 3.3.2
    3.7.18+20180528 Add subscribe page id as a search field on the attribute form
    3.7.17+20180528 Add class to translate text on frontend pages
    3.7.16+20180525 Update plugin translations for all languages on logout
    3.7.15+20180402 Minor internal changes
    3.7.14+20180328 Display description and documentation url on the plugins page
    3.7.13+20180321 Coding standards changes
    3.7.12+20180320 Minor internal changes
    3.7.11+20180129 Add image for external URL
    3.7.10+20180102 Fix bug introduced in previous change
    3.7.9+20171228  Fix bug in displaying email address
    3.7.8+20171220  Bug fix for Paginator class
    3.7.7+20171217  Minor changes
    3.7.6+20171210  Added Paginator class
    3.7.5+20171204  Minor addition to logging
    3.7.4+20171201  Minor bug fix
    3.7.3+20171125  Minor bug fix
    3.7.2+20171116  Update plugin translations on logout
    3.7.1+20171111  Add DAO method to confirm user
    3.7.0+20170929  Add class to send emails using curl and multi-curl
    3.6.8+20170914  Add Japanese language file
    3.6.7+20170911  Added Context class
    3.6.6+20170906  Allow other plugins to process the deletion of a campaign
    3.6.5+20170811  Add dependency on using mysqli
    3.6.4+20170625  Add DIC dependencies for Common Plugin
    3.6.3+20170601  Include picotainer package
    3.6.2+20170516  Improve reporting of progress when exporting
    3.6.1+20170414  Display export dialog with bootlist theme
    3.6.0+20170409  Now exports in a similar way to core phplist
    3.5.16+20170402 Remove the PicoFeed package
    3.5.15+20170304 Make help dialog compatible with new phplist theme
    3.5.14+20170302 Improve pager css
    3.5.13+20170223 Copy rows in the listmessage table when copying a message
    3.5.12+20170216 Use correct csrf token name
    3.5.11+20170209 Internal changes
    3.5.10+20170209 When copying a message copy rows from the messagedata table
    3.5.9+20170206  Change to copying a message for phplist 3.3.0
    3.5.8+20160818  Upgrade Picofeed package
    3.5.7+20160527  Internal rework of class autoloading
    3.5.6+20160515  Minor changes
    3.5.5+20160513  Minor changes and fixes
    3.5.4+20160330  Internal changes
    3.5.3+20160329  Internal change to correct namespace
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
