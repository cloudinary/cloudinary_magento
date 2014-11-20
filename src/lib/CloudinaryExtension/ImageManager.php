<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class ImageManager
{
    private $imageProvider;

    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    public function uploadImage($imagePath)
    {
        $image = Image::fromPath($imagePath);
        $this->imageProvider->upload($image);
    }

    public function getUrlForImage($imageName, $options = array())
    {
        return $this->imageProvider->getImageUrlByName($imageName, $options);
    }
}

