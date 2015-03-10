<?php

namespace ImageProviders;

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProvider;

class TransformingImageProvider implements ImageProvider
{

    public function upload(Image $image)
    {
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        return http_build_query($transformation->build());
    }

    public function deleteImage(Image $image)
    {
    }

    public function validateCredentials()
    {
    }

}