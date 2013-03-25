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
 * @version   SVN: $Id: toolbar.tpl.php 697 2012-03-22 10:00:15Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Template for the toolbar
 */

/**
 *
 * Available fields
 * - $buttons: array of buttons to be displayed
 */
?>
<!--
	Replace javascript help functions to allow window size to be specified
-->
<script language="javascript" type="text/javascript">
var helpwin = null;
var helploc = null;
var helpX = 100;
var helpY = 100;
var helpWidth = 350;
var helpHeight = 350;

function help(loc, width, height, X, Y) {
	helpWidth = width || helpWidth;
	helpHeight = height || helpHeight;
	helpX = X || helpX;
	helpY = Y || helpY;

	if (helpwin && !helpwin.closed) {
		helpwin.close();
		helpwin = '';
		helploc = loc;
		setTimeout("openPluginHelpDialog()",500)
	} else {
		helploc = loc;
		openPluginHelpDialog();
	}
}

function openhelp() {
	helpwin=window.open(
		helploc,
		"help",
		'screenX=' + helpX + ',screenY=' + helpY + ',width=' + helpWidth + ',height=' + helpHeight + ',scrollbars=1,location=0'
	);
	if (window.focus) 
		{helpwin.focus()}
}

function openPluginHelpDialog(url) {
  $("#dialog").dialog({
    minHeight: 400,
    maxHeight: 800,
    height: 650,
    width: 500,
    maxWidth: 650,
    modal: true,
    show: 'blind',
    hide: 'explode',
    scrollbars: 1
  });
  $("#dialog").load(url);
  $(".ui-widget-overlay").click(function() {
    $("#dialog").dialog('close');
  });
}
$(document).ready(function() {

    $(".pluginhelpdialog").unbind('click');
    $(".pluginhelpdialog").click(function() {
        openPluginHelpDialog(this.href);
        return false;
    });
});
</script>
<style type="text/css">
div.toolbar {
	float: right;
}
div.clear {
	clear: both;
}
div.toolbar img {
	margin-right: 5px;
	border: 0px;
	vertical-align: bottom;
}
</style>
<div class='toolbar'>
<?php foreach($buttons as $button) : 
	echo $button->display();
endforeach; ?>
</div>
<div class='clear'></div>
