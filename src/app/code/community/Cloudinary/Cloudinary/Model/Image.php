<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\ImageManager;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{


    public function upload($imageDetails)
    {
        $configuration = Mage::helper('cloudinary_cloudinary/configuration');

        $cloudinary = new ImageManager(new CloudinaryImageProvider(), new Configuration());
        $cloudinary->uploadImage(
            $this->_imagePathFromImageDetails($imageDetails),
            $configuration->getApiKey(),
            $configuration->getApiSecret()
        );
    }

    protected function _imagePathFromImageDetails($imageDetails)
    {
        return  preg_replace('/.tmp$/', '', $imageDetails['path'] . $imageDetails['file']);
    }
}