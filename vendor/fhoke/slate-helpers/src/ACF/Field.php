<?php

namespace Fhoke\Slate\ACF;

abstract class Field
{
    protected $field;

    public function __construct($field)
    {
        $this->field = $field;
    }
}
