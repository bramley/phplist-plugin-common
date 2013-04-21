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
 */

/**
 * Template HTML for the help page
 */
?>
<HTML>
<HEAD>
<TITLE>help</TITLE>
</HEAD>
<BODY>
<!-- content -->
<h3><?php echo $this->i18n->get('plugin_title') . ': ' . $topic ?></h3>
<?php if (isset($file)) include $file; ?>
<?php if (isset($help)) echo $help; ?>
</BODY>
</HTML>

