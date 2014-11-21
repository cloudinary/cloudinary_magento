<?php

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProvider;

class TransformingImageProvider implements ImageProvider
{

    public function upload(Image $image)
    {
        // TODO: Implement upload() method.
    }

    public function getImageUrlByName($imageName, $options = array())
    {
        // TODO: Implement getImageUrlByName() method.
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        $dimensions = $transformation->getDimensions();

        return sprintf('https://res.cloudinary.com/demo/image/upload/h_%s,w_%s/%s',
            $dimensions->getHeight(),
            $dimensions->getWidth(),
            (string)$image
        );
    }
}