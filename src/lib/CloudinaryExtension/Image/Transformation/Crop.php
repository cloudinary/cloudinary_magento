<?php

namespace CloudinaryExtension\Image\Transformation;

class Crop
{
    private $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromString($value)
    {
        return new Crop($value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
