<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config implements Cloudinary_Cloudinary_Model_Enablable
{
    private $_config;

    public function getMediaUrl($file)
    {
        if($this->isEnabled()) {
            return $this->_getUrlForImage($file);
        }

        return parent::getMediaUrl($file);
    }

    public function getTmpMediaUrl($file)
    {
        if($this->isEnabled()) {
            return $this->_getUrlForImage($file);
        }

        return parent::getTmpMediaUrl($file);
    }

    private function _getUrlForImage($file)
    {
        $cloudinary = new ImageManager(new CloudinaryImageProvider(
            $this->_config->buildCredentials(),
            Cloud::fromName($this->_config->getCloudName())
        ));

        return $cloudinary->getUrlForImage(Image::fromPath($file));
    }

    public function isEnabled()
    {
        if(is_null($this->_config)) {
            $this->_config = Mage::helper('cloudinary_cloudinary/configuration');
        }
        return $this->_config->isEnabled();
    }
}
