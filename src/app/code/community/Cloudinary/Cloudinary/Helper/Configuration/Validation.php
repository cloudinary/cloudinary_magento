<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Exception\InvalidCredentials;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;

class Cloudinary_Cloudinary_Helper_Configuration_Validation extends Mage_Core_Helper_Abstract
{

    public function validateEnvironmentVariable($environmentVariable)
    {
        $configuration = $this->_getConfiguration($environmentVariable);
        $imageProvider = CloudinaryImageProvider::fromConfiguration($configuration);

        if (!$imageProvider->validateCredentials()) {
            throw new InvalidCredentials("There was a problem validating your Cloudinary credentials.");
        }
    }

    private function _getConfiguration($environmentVariable)
    {
        return Configuration::fromEnvironmentVariable(
            CloudinaryEnvironmentVariable::fromString($environmentVariable)
        );
    }

}