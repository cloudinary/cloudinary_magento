<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Gravity;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class Cloudinary_Cloudinary_Helper_Configuration extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_ENABLED = 'cloudinary/cloud/cloudinary_enabled';

    const CONFIG_PATH_CLOUD_NAME = 'cloudinary/cloud/cloudinary_cloud_name';

    const CONFIG_DEFAULT_GRAVITY = 'cloudinary/transformations/cloudinary_gravity';

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

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
        return (string)Mage::getStoreConfig(self::CONFIG_PATH_CLOUD_NAME);
    }

    public function getDefaultGravity()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DEFAULT_GRAVITY);
    }

    public function setDefaultGravity($value)
    {
        $this->_setStoreConfig(self::CONFIG_DEFAULT_GRAVITY, $value);
    }

    public function isEnabled()
    {
        return (boolean)Mage::getStoreConfig(self::CONFIG_PATH_ENABLED);
    }

    public function enable()
    {
        $this->_setStoreConfig(self::CONFIG_PATH_ENABLED, self::STATUS_ENABLED);
    }

    public function disable()
    {
        $this->_setStoreConfig(self::CONFIG_PATH_ENABLED, self::STATUS_DISABLED);
    }

    public function buildConfiguration()
    {
        $config = Configuration::fromCloudAndCredentials(
            $this->buildCredentials(),
            Cloud::fromName($this->getCloudName())
        );

        $config->getDefaultTransformation()->withGravity(Gravity::fromString($this->getDefaultGravity()));

        return $config;
    }

    private function _setStoreConfig($configPath, $value)
    {
        $config = new Mage_Core_Model_Config();
        $config->saveConfig($configPath, $value);
        Mage::app()->getCacheInstance()->cleanType('config');

        if (Mage::getEdition() === 'Enterprise') {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(Enterprise_PageCache_Model_Processor::CACHE_TAG);
        } else {
            Mage::app()->cleanCache();
        }
    }

}