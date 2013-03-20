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
 * @version   SVN: $Id: Toolbar.php 838 2012-07-30 08:50:10Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class implements a button toolbar
 * 
 */
class CommonPlugin_Toolbar_Button
{
	public $url;
	public $icon;
	public $caption;
	
	public function display()
	{
		return CHtml::tag(
			'a', array('href' => $this->url), 
			CHtml::tag('img', array('src'=> 'images/' . $this->icon, 'alt' => $this->caption, 'title' => $this->caption))
		);	
	}
}

class CommonPlugin_Toolbar
{
	const TEMPLATE = '/toolbar.tpl.php';

	private $buttons = array();
	private $controller;

	public function __construct($controller)
	{
		$this->controller = $controller;
	}

	public function addDownloadButton(array $query = array())
	{
		$button = new CommonPlugin_Toolbar_Button;
		$button->url = CommonPlugin_PageURL::create(null, $query + array('action' => 'download'));
		$button->icon = 'excel.png';
		$button->caption = $this->controller->i18n->get('download to Excel');
		$this->buttons[] = $button;
	}

	public function addHelpButton($topic)
	{
		foreach (array(
			array('caption' => 'help', 'topic' => $topic, 'windowSize' => '500, 500', 'icon' => 'help.png'),
			array('caption' => 'about', 'topic' => 'about', 'windowSize' => '350, 410', 'icon' => 'exclamation.png'),
			array('caption' => 'phpinfo', 'topic' => 'phpinfo', 'windowSize' => '650, 800', 'icon' => 'page_white_php.png'),
			array('caption' => 'config.php', 'topic' => 'config.php', 'windowSize' => '650, 800', 'icon' => 'cog.png')
		) as $param) {
			$button = new CommonPlugin_Toolbar_Button;
			$button->url = sprintf("javascript:help('%s', %s)",
				CommonPlugin_PageURL::create(null, array('action' => 'help', 'topic' => $param['topic'])),
				$param['windowSize']
			);
			$button->icon = $param['icon'];
			$button->caption = $this->controller->i18n->get($param['caption']);
			$this->buttons[] = $button;
		}
	}

	public function display()
	{
		$params = array('buttons' => $this->buttons);
		return $this->controller->render(dirname(__FILE__) . self::TEMPLATE, $params);
	}
}
