<?php

namespace CloudinaryExtension;

class Image
{

    private $imagePath;

    public static function fromPath($anImagePath)
    {
        $image = new Image();
        $image->imagePath = $anImagePath;
        return $image;
    }

    public function __toString()
    {
        return $this->imagePath;
    }
}
