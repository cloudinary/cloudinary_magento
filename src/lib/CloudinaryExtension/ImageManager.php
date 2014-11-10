<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class ImageManager
{
    private $imageProvider;
    private $configuration;

    public function __construct(ImageProvider $imageProvider,Configuration $configuration)
    {
        $this->imageProvider = $imageProvider;
        $this->configuration = $configuration;
    }

    public function uploadImage($imagePath, $provider_key, $provider_secret)
    {
        $image = Image::fromPath($imagePath);

        $credentials = $this->getCredentials($provider_key, $provider_secret);
        $this->imageProvider->upload($image, $credentials);
    }

    public function getUrlForImage($imageName)
    {

        return $this->imageProvider->getImageUrlByName($imageName);
    }

    private function getCredentials($provider_key, $provider_secret)
    {
        $key = Key::fromString($provider_key);
        $secret = Secret::fromString($provider_secret);
        $credentials = new Credentials($key, $secret);
        return $credentials;
    }
}
