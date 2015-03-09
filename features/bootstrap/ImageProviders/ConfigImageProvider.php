<?php

namespace ImageProviders;

use CloudinaryExtension\Configuration;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProvider;

class ConfigImageProvider implements ImageProvider
{

    private $configuration;
    private $subdomains = ['cdn1', 'cdn2'];
    private $prefixCount = 0;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function upload(Image $image)
    {
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        $prefix =  $this->subdomains[$this->prefixCount % 2];

        if($this->configuration->getCdnSubdomainStatus() === true)
        {
            $this->prefixCount += 1;
        }

        return $prefix . "/" . $image;
    }

    public function deleteImage(Image $image)
    {
    }
}