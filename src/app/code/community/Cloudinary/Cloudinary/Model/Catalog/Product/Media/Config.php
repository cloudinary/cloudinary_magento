<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config implements Cloudinary_Cloudinary_Model_Enablable
{
    private $_config;
    private $_syncronisation;

    public function getMediaUrl($file)
    {
        if($this->_imageShouldComeFromCloudinary($file)) {
            return $this->_getUrlForImage($file);
        }

        return parent::getMediaUrl($file);
    }

    public function getTmpMediaUrl($file)
    {
        if($this->_imageShouldComeFromCloudinary($file)) {
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



    private function _isEnabled()
    {
        return $this->_getConfigHelper()->isEnabled();
    }

    private function _isImageInCloudinary($imageName)
    {
        return $this->_getSynchronisationModel()->isImageInCloudinary($imageName);
    }

    private function _getConfigHelper()
    {
        if (is_null($this->_config)) {
            $this->_config = Mage::helper('cloudinary_cloudinary/configuration');
        }
        return $this->_config;
    }

    private function _getSynchronisationModel()
    {
        if (is_null($this->_syncronisation)) {
            $this->_syncronisation = Mage::getModel('cloudinary_cloudinary/synchronisation');
        }
        return $this->_syncronisation;
    }

    private function _imageShouldComeFromCloudinary($file)
    {
        return $this->_isEnabled() && $this->_isImageInCloudinary($file);
    }
}
