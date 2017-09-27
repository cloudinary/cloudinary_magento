<?php

namespace ImageProviders;

use CloudinaryExtension\Configuration;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProvider;

class ConfigImageProvider implements ImageProvider
{

    private $configuration;
    private $subdomains = ['cdn1', 'cdn2'];
    private $prefixCount = 0;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function upload(Image $image)
    {
    }

    public function retrieveTransformed(Image $image, Transformation $transformation)
    {
        $prefix =  $this->subdomains[$this->prefixCount % 2];

        if($this->configuration->getCdnSubdomainStatus() === true)
        {
            $this->prefixCount += 1;
        }

        return $prefix . "/" . $image;
    }

    public function retrieve(Image $image)
    {

    }

    public function delete(Image $image)
    {
    }

    public function validateCredentials()
    {
    }
}