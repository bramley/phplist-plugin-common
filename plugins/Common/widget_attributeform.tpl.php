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
 * Template for the attribute search and select widget form.
 */

/**
 * Available fields
 * - $showSearch: whether to show the search fields
 * - $model: the model
 *        searchTerm: term to search on
 *        searchBy: attribute to search on
 *        attributes: associative array of user attributes
 *        listID: selected list to filter
 *        lists: associative array of lists
 *        selectedAttrs: array of selected attributes
 *        unconfirmed: show only unconfirmed users
 *        blacklisted: show only blacklisted users.
 */
?>
<style type="text/css">
div.inline {
    display: inline;
    white-space: nowrap
}
.inline label, label.inline{
    display: inline;
}
input[type="text"], select {
    width: auto !important;
    display: inline !important;
}
</style>
<form method='post'>
    <fieldset>
        <?php if ($showSearch): ?>
        <div style='padding-bottom: 10px;'>
            <label class="inline">
        <?php echo s('Search for'); ?>:
            <input size="24" type="text" value="<?php echo htmlspecialchars($model->searchTerm); ?>" 
                name="SearchForm[searchTerm]" id="SearchForm_searchTerm" />
            </label>
            <div class='inline'>
            <?php echo CHtml::dropDownList(
            'SearchForm[searchBy]', $model->searchTerm == '' ? 'email' : $model->searchBy,
            array('email' => 'email', 'id' => 'id', 'uniqid' => 'unique id', 'subspage' => 'subscribe page id') + CHtml::listData($model->attributes, 'id', 'name')
            ); ?>&nbsp;
            </div>
            <label class="inline">
            <?php echo s('List'); ?>:
            <?php echo CHtml::dropDownList(
                'SearchForm[listID]', $model->listID,
                CHtml::listData($model->lists, 'id', 'name'),
                array('prompt' => s('All'))
            ); ?>
            </label>

            <div class='inline'>
                <label>
            <?php echo s('Confirmed'); ?>:
            <?php echo CHtml::dropDownList(
                'SearchForm[confirmed]', $model->confirmed,
                array(1 => s('All subscribers'), 2 => s('confirmed only'), 3 => s('unconfirmed only'))
            ); ?>
                </label>
                <label>
            <?php echo s('Blacklisted'); ?>:
            <?php echo CHtml::dropDownList(
                'SearchForm[blacklisted]', $model->blacklisted,
                array(1 => s('All subscribers'), 2 => s('blacklisted only'), 3 => s('not blacklisted only'))
            ); ?>
                </label>
            </div>
        </div>
        <?php endif; ?>
        <div class='clear'></div>
        <div>
        <?php echo CHtml::checkBoxList(
            'SearchForm[selectedAttrs]',
            $model->selectedAttrs,
             CHtml::listData($model->attributes, 'id', 'name'),
            array(
                'separator' => ' ', 'uncheckValue' => 0,
                'template' => '<div class="inline">{input} {label}</div>',
            )
        ); ?>
            <input type='submit' name='SearchForm[submit]' value='<?php echo s('Show'); ?>' />
        </div>
    </fieldset>
</form>
