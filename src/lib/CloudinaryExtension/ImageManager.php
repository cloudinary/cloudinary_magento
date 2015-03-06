<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Format;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class ImageManager
{
    private $imageProvider;

    /**
     * @var Transformation
     */
    private $defaultTransformation;

    public function __construct(ImageProvider $imageProvider, Transformation $defaultTransformation)
    {
        $this->imageProvider = $imageProvider;
        $this->defaultTransformation = $defaultTransformation;
    }

    public function uploadImage($imagePath)
    {
        $image = Image::fromPath($imagePath);
        $this->imageProvider->upload($image);
    }

    public function getUrlForImage(Image $image)
    {
        return $this->getUrlForImageWithTransformation($image, $this->defaultTransformation);
    }

    public function getUrlForImageWithTransformation(Image $image, Transformation $transformation)
    {
        $transformation->withFormat(Format::fromExtension($image->getExtension()));

        $image = $this->imageProvider->transformImage($image, $transformation);

        return (string)$image;
    }

    public function deleteImage(Image $image)
    {
        $this->imageProvider->deleteImage($image);
    }

    public function getDefaultTransformation()
    {
        return $this->defaultTransformation;
    }
}
