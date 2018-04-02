<?php

namespace phpList\plugin\Common;

use CHtml;

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
 * This class implements a button toolbar.
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
            new PageURL('help', array('pi' => $_GET['pi'], 'topic' => 'about')),
            'gnu_licence.png',
            $this->controller->i18n->get('about'),
            array('class' => 'helpdialog', 'target' => '_blank', 'style' => 'background: none; display: inline;')
        );
    }

    public function addExportButton(array $query = array())
    {
        $this->buttons[] = new ToolbarButton(
            PageURL::createFromGet($query + array('action' => 'exportCSV')),
            'excel.png',
            $this->controller->i18n->get('export'),
            ['class' => 'dialog']
        );
    }

    public function addHelpButton($topic)
    {
        $this->buttons[] = new ToolbarButton(
            new PageURL('help', array('pi' => $_GET['pi'], 'topic' => $topic)),
            'info.png',
            $this->controller->i18n->get('help'),
            array('class' => 'helpdialog', 'target' => '_blank', 'style' => 'background: none; display: inline;')
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
