<?php

namespace CloudinaryExtension\Image\Transformation;

class Format
{
    private $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromExtension($value)
    {
        return new Format($value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
