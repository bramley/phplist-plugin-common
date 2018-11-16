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
 * Template HTML for the help page.
 */
?>
<HTML>
<HEAD>
<TITLE>help</TITLE>
</HEAD>
<BODY>
<style type="text/css">
div.help td {
    border: dotted gray 1px;
    padding: 1px;
}
</style>
<h3><?php echo $this->i18n->get('plugin_title') . ': ' . $topic; ?></h3>
<div class="help">
<?php if (isset($file)) {
    include $file;
} ?>
<?php if (isset($help)) {
    echo $help;
} ?>
</div>
</BODY>
</HTML>

