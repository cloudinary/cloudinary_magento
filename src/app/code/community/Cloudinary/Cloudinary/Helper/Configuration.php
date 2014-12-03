<?php

use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class Cloudinary_Cloudinary_Helper_Configuration extends Mage_Core_Helper_Abstract
{
    private $_isExtensionEnabled;

    public function getApiKey()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig('cloudinary/credentials/cloudinary_api_key'));
    }

    public function getApiSecret()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig('cloudinary/credentials/cloudinary_api_secret'));
    }

    public function buildCredentials()
    {
        $key = Key::fromString($this->getApiKey());
        $secret = Secret::fromString($this->getApiSecret());

        return new Credentials($key, $secret);
    }

    public function getCloudName()
    {
        return (string)Mage::getStoreConfig('cloudinary/cloud/cloudinary_cloud_name');
    }

    public function isEnabled()
    {
        return (boolean)Mage::getStoreConfig('cloudinary/cloud/cloudinary_enabled');
    }
}