<?php

use CloudinaryExtension\ImageManagerFactory;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function upload(array $imageDetails)
    {
        $imageManager = ImageManagerFactory::buildFromConfiguration(
            Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()
        );

        $imageManager->uploadImage($this->_imageFullPathFromImageDetails($imageDetails));

        Mage::getModel('cloudinary_cloudinary/synchronisation')->tagImageAsBeingInCloudinary($imageDetails);
    }

    private function _imageFullPathFromImageDetails($imageDetails)
    {
        return  $this->_getMediaBasePath() . $this->_getImageDetailFromKey($imageDetails, 'file');
    }

    private function _getImageDetailFromKey(array $imageDetails, $key)
    {
        if (!array_key_exists($key, $imageDetails)) {
            throw new Cloudinary_Cloudinary_Model_Exception_BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }

    private function _getMediaBasePath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }
}
