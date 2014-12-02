<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    private $_config;

    public function getUrl()
    {
        if($this->_isEnabled()) {
            $cloudinary = new ImageManager(new CloudinaryImageProvider(
                $this->getConfigHelper()->buildCredentials(),
                Cloud::fromName($config->getCloudName())
            ));

            return $cloudinary->getUrlForImage(Image::fromPath($this->_newFile));
        }

        return parent::getUrl();
    }

    private function _isEnabled()
    {
        return $this->getConfigHelper()->isEnabled();
    }

    private function getConfigHelper()
    {
        if(is_null($this->_config)) {
            $this->_config = Mage::helper('cloudinary_cloudinary/configuration');
        }
        return $this->_config;
    }
}