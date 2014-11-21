<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Dimension;

class Image
{

    private $imagePath;

    private $dimensions;

    private function __construct($imagePath, Dimension $dimensions = null)
    {
        $this->imagePath = $imagePath;
        $this->dimensions = $dimensions;
    }

    public static function fromPath($anImagePath)
    {
        return new Image($anImagePath);
    }

    public static function fromPathAndDimensions($anImagePath, Dimension $dimensions)
    {
        return new Image($anImagePath, $dimensions);
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
