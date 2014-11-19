<?php

namespace CloudinaryExtension;

class Image
{

    private $imagePath;

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
}
