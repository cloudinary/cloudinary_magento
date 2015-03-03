<?php

namespace CloudinaryExtension\Image;

class Gravity
{
    private $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function fromString($value)
    {
        return new Gravity($value);
    }
}
