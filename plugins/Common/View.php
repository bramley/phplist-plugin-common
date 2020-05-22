<?php

namespace phpList\plugin\Common;

class View
{
    private $template;
    private $fields;

    public function __construct($template, array $fields = [])
    {
        if (!is_file($template)) {
            throw new FileNotFoundException($template);
        }
        $this->template = $template;
        $this->fields = $fields;
    }

    public function __toString()
    {
        extract($this->fields);
        ob_start();

        require $this->template;

        return ob_get_clean();
    }

    public function render(array $fields = [])
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this->__toString();
    }
}
