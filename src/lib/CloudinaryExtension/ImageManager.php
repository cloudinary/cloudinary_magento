<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Dimension;
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
        $options = array();

        if ($image->getDimensions()) {
            $options['width'] = $image->getDimensions()->getWidth();
            $options['height'] = $image->getDimensions()->getHeight();
            $options['crop'] = 'pad';
        }

        return $this->imageProvider->getImageUrlByName((string)$image, $options);
    }
}
