<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Exception\InvalidCredentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class Cloudinary_Cloudinary_Helper_Configuration_Validation extends Mage_Core_Helper_Abstract
{

    public function validateCredentials($cloudName, $apiKey, $apiSecret)
    {
        $key = Key::fromString($apiKey);
        $secret = Secret::fromString($apiSecret);

        $configuration = Configuration::fromCloudAndCredentials(
            new Credentials($key, $secret),
            Cloud::fromName($cloudName)
        );

        $imageProvider = CloudinaryImageProvider::fromConfiguration($configuration);

        if (!$imageProvider->validateCredentials()) {
            throw new InvalidCredentials("There was a problem validating your Cloudinary credentials.");
        }
    }

}