<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class ImageManager
{
    private $imageProvider;

    private $dimension;

    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    public function uploadImage($imagePath)
    {
        $image = Image::fromPath($imagePath);
        $this->imageProvider->upload($image);
    }

    public function getUrlForImage(Image $image)
    {
        return $this->imageProvider->getImageUrlByName((string)$image);
    }

    public function getUrlForImageWithTransformation(Image $image, Transformation $transformation)
    {
        $image = $this->imageProvider->transformImage($image, $transformation);


        return (string)$image;
    }

}
