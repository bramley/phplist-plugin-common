<?php

global $plugins, $pagefooter, $ui, $THEMES;

$themeDir = isset($THEMES[$ui]['parent']) ? $THEMES[$ui]['parent'] : $ui;

if (is_readable($f = $plugins['CommonPlugin']->coderoot . "ui/$themeDir/dialog.js")) {
    $pagefooter[__FILE__] = file_get_contents($f);
}
