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
 * Template for the toolbar.
 *
 * Available fields
 * - $buttons: array of buttons to be displayed
 */
?>

<style type="text/css">
div.toolbar {
    float: right;
}
div.toolbar img {
    margin-right: 5px;
    border: 0px;
    vertical-align: bottom;
}
.clear {
    clear: both;
}
.glyphicon {
    top: 5px;
}
.modal-dialog {
     width: 40%;
}
</style>
<div class='toolbar'>
<?php foreach ($buttons as $button) :
    echo $button->display();
endforeach; ?>
</div>
<div class='clear'></div>
<?php

global $plugins, $pagefooter, $ui, $THEMES;

if (isset($THEMES[$ui]['parentdir'])) {
    $dir = $THEMES[$ui]['parentdir'];
} else {
    $dir = $ui;
}

if (is_readable($f = $plugins['CommonPlugin']->coderoot . "ui/$dir/dialog.js")) {
    $pagefooter[__FILE__] = file_get_contents($f);
}
