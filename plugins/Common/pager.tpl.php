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
<?php
require_once __DIR__ . '/pager.css';
?>
<div class="paging" id="paging">
    <div class='pagerinline left' style='width: 33.333%;'><?php echo $range; ?></div>
    <div class='pagerinline center' style='width: 33.333%;'><?php echo $show; ?></div>
<?php if (isset($prev)): ?>
    <div class='pagerinline center' style='width: 10%;'><?php echo $prev; ?> | <?php echo $next; ?></div>
<?php endif; ?>
<?php if (isset($first)): ?>
    <div class='controls right'><?= $first, $back, $forward, $last; ?></div>
<?php endif; ?>
</div>
