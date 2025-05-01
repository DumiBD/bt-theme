<?php

namespace Fhoke\Slate\Templating\Views;

class View
{
    protected $views;

    public function __construct()
    {
        $this->views = new Views();
    }

    public static function render(string $file, array $args = [])
    {
        return (new self)->views->render($file, $args);
    }

    public static function present(string $file, array $args = [])
    {
        return (new self)->views->present($file, $args);
    }
}
