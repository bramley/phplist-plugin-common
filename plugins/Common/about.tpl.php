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
 * Template HTML for the "about" content within the help page.
 */
?>
<?php
foreach ($plugins as $p): ?>
    <p><?php echo "{$p['name']} - version {$p['version']}"; ?> </p>
<?php
endforeach;
?>
<h3>Licence</h3>
<?php if (isset($pluginLicence)) {
    echo $pluginLicence;
} ?>

