<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\ImageManager;
use CloudinaryExtension\ImageManagerFactory;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{

    private $_configuration;

    public function upload(array $imageDetails)
    {
        $imageManager = ImageManagerFactory::fromConfiguration(Mage::helper('cloudinary_cloudinary/configuration'));

        $imageManager->uploadImage($this->_imageFullPathFromImageDetails($imageDetails));

        Mage::getModel('cloudinary_cloudinary/synchronisation')->tagImageAsBeingInCloudinary($imageDetails);
    }

    private function _imageFullPathFromImageDetails($imageDetails)
    {
        return  $this->_getMediaBasePath() . $this->_getImageDetailFromKey($imageDetails, 'file');
    }

    private function _getImageDetailFromKey(array $imageDetails, $key)
    {
        if(!array_key_exists($key, $imageDetails)) {
            throw new Cloudinary_Cloudinary_Model_Exception_BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }

    private function _getCloudName()
    {
        return Cloud::fromName($this->_getConfigurationHelper()->getCloudName());
    }

    private function _getConfigurationHelper()
    {
        if($this->configuration === null) {
            $this->configuration = Mage::helper('cloudinary_cloudinary/configuration');
        }
        return $this->configuration;
    }
}