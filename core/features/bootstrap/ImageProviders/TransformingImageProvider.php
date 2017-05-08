<?php

namespace ImageProviders;

use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProvider;

class TransformingImageProvider implements ImageProvider
{

    private $images = array();

    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function upload(Image $image)
    {
        $this->images[(string)$image] = $image;
    }

    public function retrieveTransformed(Image $image, Transformation $transformation)
    {
        return http_build_query($transformation->build()) .'/'. $this->images[(string)$image];
    }

    public function retrieve(Image $image)
    {
        return $this->retrieveTransformed($image, $this->configuration->getDefaultTransformation());
    }

    public function delete(Image $image)
    {
    }

    public function validateCredentials()
    {
    }

}