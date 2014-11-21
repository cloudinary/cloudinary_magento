<?php

namespace CloudinaryExtension\Image;

class Dimension
{

    private $width;
    private $height;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public static function fromWithAndHeight($width, $height)
    {
        return new Dimension($width, $height);
    }
}
