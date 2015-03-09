<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Exception\InvalidCredentials;
use CloudinaryExtension\ImageProviderFactory;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class Cloudinary_Cloudinary_Helper_Configuration_Validation extends Mage_Core_Helper_Abstract
{
    private $_providerName;

    public function __construct($providerName = 'cloudinary')
    {
        $this->_providerName = $providerName;
    }

    public function validateCredentials($cloudName, $apiKey, $apiSecret)
    {
        $key = Key::fromString($apiKey);
        $secret = Secret::fromString($apiSecret);

        $imageProvider = ImageProviderFactory::fromProviderName(
            $this->_providerName,
            new Credentials($key, $secret),
            Cloud::fromName($cloudName)
        );

        if (!$imageProvider->validateCredentials()) {
            throw new InvalidCredentials("There was a problem validating your Cloudinary credentials.");
        }
    }

}