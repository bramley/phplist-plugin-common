<?php

namespace phpList\plugin\Common;

use CHtml;

/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2017 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class implements a button toolbar
 * 
 */
class ToolbarButton
{
    public $url;
    public $icon;
    public $caption;
    public $attributes = array();
    
    public function display()
    {
        $this->attributes['href'] = $this->url;
        return CHtml::tag('a', $this->attributes, new ImageTag($this->icon, $this->caption));
    }
}

class Toolbar
{
    const TEMPLATE = '/toolbar.tpl.php';

    private $buttons = array();
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function addExportButton(array $query = array())
    {
        $button = new ToolbarButton;
        $button->url = new PageURL(null, $query + array('action' => 'exportCSV'));
        $button->icon = 'excel.png';
        $button->caption = $this->controller->i18n->get('export');
        $this->buttons[] = $button;
    }

    public function addHelpButton($topic)
    {
        foreach (array(
            array('caption' => 'help', 'topic' => $topic, 'icon' => 'info.png', 'class' => 'pluginhelpdialog'),
            array('caption' => 'about', 'topic' => 'about', 'icon' => 'gnu_licence.png', 'class' => 'pluginhelpdialog')
        ) as $param) {
            $button = new ToolbarButton;
            $button->url = new PageURL(null, array('action' => 'help', 'topic' => $param['topic']));
            $button->icon = $param['icon'];
            $button->caption = $this->controller->i18n->get($param['caption']);
            $button->attributes = array('class' => $param['class'], 'target' => '_blank');
            $this->buttons[] = $button;
        }
    }

    public function display()
    {
        $params = array('buttons' => $this->buttons);
        return $this->controller->render(dirname(__FILE__) . self::TEMPLATE, $params);
    }
}
