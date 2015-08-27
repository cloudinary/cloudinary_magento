<?php

namespace CloudinaryExtension;

class Image
{
    private $imagePath;
    private $relativePath;
    private $pathParts;

    private function __construct($imagePath, $relativePath = '')
    {
        $this->imagePath = $imagePath;
        $this->relativePath = $relativePath;
        $this->pathParts = pathinfo(basename($this->imagePath));
    }

    public static function fromPath($anImagePath, $relativepath = '')
    {
        return new Image($anImagePath, $relativepath);
    }

    public function __toString()
    {
        return $this->imagePath;
    }

    public function getRelativePath()
    {
        return $this->relativePath;
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
