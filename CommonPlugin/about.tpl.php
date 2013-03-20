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
 * @version   SVN: $Id: about.tpl.php 800 2012-07-12 22:06:32Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * Template HTML for the "about" content within the help page
 */
?>
<?php
foreach ($plugins as $p): ?>
	<p><?php echo "{$p['name']} - version {$p['version']}"; ?> </p>
<?php
endforeach;
?>
<h3>Licence</h3>
<?php if(isset($pluginLicence)) echo $pluginLicence; ?>

