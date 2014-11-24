<?php

namespace CloudinaryExtension\Image;

class Dimensions
{

    private $width;
    private $height;

    private function __construct($width, $height)
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

    public static function fromWidthAndHeight($width, $height)
    {
        return new Dimensions($width, $height);
    }
}
