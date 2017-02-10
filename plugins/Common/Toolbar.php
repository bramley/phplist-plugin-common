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
    private $url;
    private $icon;
    private $caption;
    private $attributes;
    
    public function __construct($url, $icon, $caption, $attributes = [])
    {
        $this->url = $url;
        $this->icon = $icon;
        $this->caption = $caption;
        $this->attributes = $attributes;
    }

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

    public function addAboutButton()
    {
        $this->buttons[] = new ToolbarButton(
            new PageURL(null, array('action' => 'help', 'topic' => 'about')),
            'gnu_licence.png',
            $this->controller->i18n->get('about'),
            array('class' => 'pluginhelpdialog', 'target' => '_blank')
        );
    }

    public function addExportButton(array $query = array())
    {
        $this->buttons[] = new ToolbarButton(
            new PageURL(null, $query + array('action' => 'exportCSV')),
            'excel.png',
            $this->controller->i18n->get('export')
        );
    }

    public function addHelpButton($topic)
    {
        $this->buttons[] = new ToolbarButton(
            new PageURL(null, array('action' => 'help', 'topic' => $topic)),
            'info.png',
            $this->controller->i18n->get('help'),
            array('class' => 'pluginhelpdialog', 'target' => '_blank')
        );
        $this->addAboutButton();
    }

    public function addExternalHelpButton($url)
    {
        $this->buttons[] = new ToolbarButton(
            $url,
            'info.png',
            $this->controller->i18n->get('help'),
            array('target' => '_blank')
        );
        $this->addAboutButton();
    }

    public function display()
    {
        $params = array('buttons' => $this->buttons);

        return $this->controller->render(dirname(__FILE__) . self::TEMPLATE, $params);
    }
}
