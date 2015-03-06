<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
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

    public function validateCredentials($apiKey, $apiSecret, $cloudName)
    {
        $key = Key::fromString($apiKey);
        $secret = Secret::fromString($apiSecret);

        $imageProvider = ImageProviderFactory::fromProviderName(
            $this->_providerName,
            new Credentials($key, $secret),
            Cloud::fromName($cloudName)
        );

        $imageProvider->validateCredentials();

    }

}