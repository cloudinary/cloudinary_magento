<?php

namespace ImageProviders;

use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProvider;

class TransformingImageProvider implements ImageProvider
{

    private $images = array();

    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function upload(Image $image)
    {
        $this->images[(string)$image] = $image;
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        return http_build_query($transformation->build()) .'/'. $this->images[(string)$image];
    }

    public function deleteImage(Image $image)
    {
    }

    public function validateCredentials()
    {
    }

}