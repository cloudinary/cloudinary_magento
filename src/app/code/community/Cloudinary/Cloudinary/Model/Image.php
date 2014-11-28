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

    public function upload(array $imageDetails)
    {
        $imageManager = ImageManagerFactory::fromConfiguration(Mage::helper('cloudinary_cloudinary/configuration'));

        $imageManager->uploadImage($this->_imageFullPathFromImageDetails($imageDetails));
    }

    private function _imageFullPathFromImageDetails($imageDetails)
    {
        return  $this->_getImageDetailFromKey($imageDetails, 'path') . $this->_getImageDetailFromKey($imageDetails, 'file');
    }

    private function _getImageDetailFromKey(array $imageDetails, $key)
    {
        if(!array_key_exists($key, $imageDetails)) {
            throw new BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }
}