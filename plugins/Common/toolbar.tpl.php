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
 * Template for the toolbar
 *
 * Available fields
 * - $buttons: array of buttons to be displayed
 */

/**
 *   Replace javascript help functions to allow window size to be specified
 */
global $pagefooter;

$pagefooter[basename(__FILE__)] = <<<END
<script type='text/javascript'>
function openPluginHelpDialog(url, width, height) {
  $("#dialog").dialog({
    minHeight: 400,
    maxHeight: 800,
    height: height,
    width: width,
    maxWidth: 650,
    modal: true,
    show: 'blind',
    hide: 'explode',
    scrollbars: 1
  });
  $("#dialog").load(url);
  $(".ui-widget-overlay").click(function() {
    $("#dialog").dialog('close');
  });
}

$(document).ready(function() {
    $(".pluginhelpdialog").unbind('click');
    $(".pluginhelpdialog").click(function() {
        openPluginHelpDialog(this.href, 500, 650);
        return false;
    });
    $(".pluginhelpdialogwide").unbind('click');
    $(".pluginhelpdialogwide").click(function() {
        openPluginHelpDialog(this.href, 650, 800);
        return false;
    });
});
</script>
END
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
</style>
<div class='toolbar'>
<?php foreach ($buttons as $button) :
    echo $button->display();
endforeach; ?>
</div>
<div class='clear'></div>
