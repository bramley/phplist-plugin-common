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
 * @version   SVN: $Id: helpmanager.tpl.php 839 2012-07-30 14:56:41Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Template HTML for the help page
 */
?>
<HTML>
<HEAD>
<TITLE>help</TITLE>
<link rel="stylesheet" type="text/css" href="styles/styles_help.css">
</HEAD>
<BODY>
<style type="text/css">
body, p, td {
    font-family: sans-serif;
    font-size: 10px;
}
table{
    margin: 5px;
    border-collapse: collapse;
}
td {
    vertical-align: top;
    padding: 5px 5px 5px 5px;
    border-style: dotted;
    border-width: 1px; 
}
</style>
<!-- content -->
<h3><?php echo $this->i18n->get('plugin_title') . ': ' . $topic ?></h3>
<?php if (isset($file)) include $file; ?>
<?php if (isset($help)) echo $help; ?>
<HR WIDTH="75%">
<A HREF="Javascript:close()"><?php echo $this->i18n->get('closewindow'); ?></A>
</BODY>
</HTML>

