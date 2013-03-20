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
 * @version   SVN: $Id: Widget.php 686 2012-03-20 17:33:56Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class provides customised links and html.
 * 
 */ 
class CommonPlugin_Widget
{
	const HELP_TEMPLATE = '/widget_help.tpl.php';
	const DOWNLOAD_TEMPLATE = '/widget_download.tpl.php';
	const ATTRIBUTEFORM_TEMPLATE = '/widget_attributeform.tpl.php';
	/*
	 *	Private methods
	 */
	private function __construct()
	{
	}
	/*
	 *	Public methods
	 */
	static public function attributeForm(CommonPlugin_BaseController $controller, $model, $search = true, $select = true)
	{
		$params = array(
			'model' => $model,
			'showSearch' => $search,
			'showSelect' => $select
		);
		return $controller->render(dirname(__FILE__) . self::ATTRIBUTEFORM_TEMPLATE, $params);
	}

}
