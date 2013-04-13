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
 * @version   SVN: $Id: pager.tpl.php 778 2012-06-08 15:05:30Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */


/**
 * Template file for the Pager class
 * 
 */

/**
 *
 * Available fields
 * - range: message displaying range of items
 * - show: selector for items per page
 * - first: link to page containing first item
 * - previous: link to previous page of items
 * - next: link to next page of items
 * - last: link to last page of items
 * - webbler: WebblerListing instance
 * - message: optional message when there are no results to display
 */
?>
<style type="text/css">
/*
 *
 * Over-ride core style definitions to allow left, center and right alignment within a listing
 * and for the default to be left aligned
 *
 */
td.listinghdname, td.listinghdelement {
  text-align : left !important;
}
td.listingname, td.listingelement, td.listingelementleft, td.listingelementcenter, td.listingelementright {
  padding: 2px;
}
td.listingname, td.listingelement, td.listingelementleft {
  text-align: left !important;
}
td.listingelementcenter {
  text-align: center !important;
}
td.listingelementright {
  text-align: right !important;
}
/*
 * Copy of buttonpublish styles from phplist.css
 */
a.button:link, a.button:visited {
	font-family: verdana, sans-serif;
	font-size : 11px;
	color : #999966;
	background-color : #ffffff;
	font-weight: bold;
	text-align : middle;
	text-decoration : none;
	border: 1px #999966 solid;
	padding: 2px;
	margin: 2px;
}
a.button:hover {
	color : #666633;
}
.content table {
    table-layout: auto;
}
div.pager {
    border: 0px;
    padding-bottom: 0px;
}
.pager div.inline {
    float: left;
}
</style>
<div class='pager'>
    <div class='inline' style='width: 33%'><?php echo $range ?></div>
	<div class='inline' style='width: 34%; text-align: center'><?php echo $show ?></div>
<?php if (isset($prev)): ?>
    <div class='inline' style='width: 23%; text-align: right'><?php echo $first ?> | <?php echo $back ?> | <?php echo $forward ?> | <?php echo $last ?></div>
    <div class='inline' style='width: 10%; text-align: center'><?php echo $prev; ?> | <?php echo $next; ?></div>
<?php else: ?>
	<div class='inline' style='width: 33%; text-align: right'><?php echo $first ?> | <?php echo $back ?> | <?php echo $forward ?> | <?php echo $last ?></div>
<?php endif; ?>
</div>

