<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2016 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
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
 * - first: link to first page of items
 * - back: link to previous page of items
 * - forward: link to next page of items
 * - last: link to the final page of items
 * - prev: link to same page for the previous message
 * - next: link to same page for the next message
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

