<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Dimension;

class Image
{

    private $imagePath;

    private $dimension;

    private function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public static function fromPath($anImagePath)
    {
        return new Image($anImagePath);
    }

    public function __toString()
    {
        return $this->imagePath;
    }

    public function setDimensions(Dimension $dimension)
    {
        $this->dimension = $dimension;
    }

    public function getDimensions()
    {
        return $this->dimension;
    }
}
