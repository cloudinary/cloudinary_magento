<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Dimensions;

class Image
{

    private $imagePath;

    private $dimensions;

    private function __construct($imagePath, Dimensions $dimensions = null)
    {
        $this->imagePath = $imagePath;
        $this->dimensions = $dimensions;
    }

    public static function fromPath($anImagePath)
    {
        return new Image($anImagePath);
    }

    public function __toString()
    {
        return $this->imagePath;
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }
}
