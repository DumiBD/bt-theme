<?php

namespace Fhoke\Slate\ACF;

class Button extends Link
{
    public function __construct($field, array $args = [])
    {
        parent::__construct($field, $args);

        $this->cssClasses[] = 'btn';
    }
}
