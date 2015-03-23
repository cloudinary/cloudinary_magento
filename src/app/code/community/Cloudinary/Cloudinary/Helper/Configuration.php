<?php

use CloudinaryExtension\Configuration;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dpr;
use CloudinaryExtension\Image\Transformation\FetchFormat;
use CloudinaryExtension\Image\Transformation\Gravity;
use CloudinaryExtension\Image\Transformation\Quality;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;

class Cloudinary_Cloudinary_Helper_Configuration extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_ENABLED = 'cloudinary/cloud/cloudinary_enabled';

    const CONFIG_PATH_ENVIRONMENT_VARIABLE = 'cloudinary/setup/cloudinary_environment_variable';

    const CONFIG_DEFAULT_GRAVITY = 'cloudinary/transformations/cloudinary_gravity';

    const CONFIG_DEFAULT_QUALITY = 'cloudinary/transformations/cloudinary_image_quality';

    const CONFIG_DEFAULT_DPR = 'cloudinary/transformations/cloudinary_image_dpr';

    const CONFIG_DEFAULT_FETCH_FORMAT = 'cloudinary/transformations/cloudinary_fetch_format';

    const CONFIG_CDN_SUBDOMAIN = 'cloudinary/configuration/cloudinary_cdn_subdomain';

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    public function buildCredentials()
    {
        $environmentVariable = CloudinaryEnvironmentVariable::fromString($this->getEnvironmentVariable());
        return $environmentVariable->getCredentials();
    }

    public function getEnvironmentVariable()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig(self::CONFIG_PATH_ENVIRONMENT_VARIABLE));
    }

    public function getDefaultGravity()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DEFAULT_GRAVITY);
    }

    public function setDefaultGravity($value)
    {
        $this->_setStoreConfig(self::CONFIG_DEFAULT_GRAVITY, $value);
    }

    public function setFetchFormat($value)
    {
        $this->_setStoreConfig(self::CONFIG_DEFAULT_FETCH_FORMAT, $this->_getFetchFormatFlag($value));
    }

    public function getFetchFormat()
    {
        return Mage::getStoreConfig(self::CONFIG_DEFAULT_FETCH_FORMAT) === "1" ? FetchFormat::FETCH_FORMAT_AUTO : null;
    }

    public function setImageQuality($value)
    {
        $this->_setStoreConfig(self::CONFIG_DEFAULT_QUALITY, $value);
    }

    public function getImageQuality()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DEFAULT_QUALITY);
    }

    public function getImageDpr()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DEFAULT_DPR);
    }

    public function getCdnSubdomainFlag()
    {
        return (boolean)Mage::getStoreConfig(self::CONFIG_CDN_SUBDOMAIN);
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
        $config = Configuration::fromEnvironmentVariable(
            CloudinaryEnvironmentVariable::fromString($this->getEnvironmentVariable())
        );

        if($this->getCdnSubdomainFlag()) {
            $config->enableCdnSubdomain();
        }

        $config->getDefaultTransformation()
            ->withGravity(Gravity::fromString($this->getDefaultGravity()))
            ->withFetchFormat(FetchFormat::fromString($this->getFetchFormat()))
            ->withQuality(Quality::fromString($this->getImageQuality()))
            ->withDpr(Dpr::fromString($this->getImageDpr()))
        ;

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

    private function _getFetchFormatFlag($value)
    {
        return $value === Format::FETCH_FORMAT_AUTO ? 1 : 0;
    }

}