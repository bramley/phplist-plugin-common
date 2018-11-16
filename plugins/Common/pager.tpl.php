<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @author    Duncan Cameron
 * @copyright 2011-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * Template file for the Pager class.
 */

/**
 * Available fields
 * - range: message displaying range of items
 * - show: selector for items per page
 * - first: link to first page of items
 * - back: link to previous page of items
 * - forward: link to next page of items
 * - last: link to the final page of items
 * - prev: link to same page for the previous message
 * - next: link to same page for the next message.
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
td.listingname, td.listingelement, td.listingelementleft, td.listingelementcenter, td.listingelementright, td.listingelementwrap {
  padding: 2px;
}
td.listingname, td.listingelement, td.listingelementleft, td.listingelementwrap {
  text-align: left !important;
}
td.listingelementcenter {
  text-align: center !important;
}
td.listingelementright {
  text-align: right !important;
}
td.listingelementwrap {
  word-break: break-all;
}


.content table {
    table-layout: auto;
}
div.pager {
    border: 0px;
    padding-bottom: 0px;
}
div.pagerinline {
    float: left;
}
.left {
    text-align: left;
}
.right {
    text-align: right;
}
.center {
    text-align: center;
}
</style>
<div class='pager'>
    <div class='pagerinline left' style='width: 33.333%;'><?php echo $range; ?></div>
    <div class='pagerinline center' style='width: 33.333%;'><?php echo $show; ?></div>
<?php if (isset($prev)): ?>
    <div class='pagerinline right' style='width: 23.333%;'><?php echo $first; ?> | <?php echo $back; ?> | <?php echo $forward; ?> | <?php echo $last; ?></div>
    <div class='pagerinline center' style='width: 10%;'><?php echo $prev; ?> | <?php echo $next; ?></div>
<?php else: ?>
    <div class='pagerinline right' style='width: 33.333%;'><?php echo $first; ?> | <?php echo $back; ?> | <?php echo $forward; ?> | <?php echo $last; ?></div>
<?php endif; ?>
</div>

