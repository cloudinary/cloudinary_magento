<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Dimensions;

class Image
{
    private $imagePath;

    private $dimensions;

    private $pathParts;

    private function __construct($imagePath, Dimensions $dimensions = null)
    {
        $this->imagePath = $imagePath;
        $this->dimensions = $dimensions;
        $this->pathParts = pathinfo(basename($this->imagePath));
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

    public function getId()
    {
        return $this->pathParts['filename'];
    }

    public function getExtension()
    {
        return $this->pathParts['extension'];
    }
}
