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
        $imageProvider = new CloudinaryImageProvider($this->_getCredentials());
        $cloudinary = new ImageManager($imageProvider);
        $cloudinary->uploadImage(
            $this->_imagePathFromImageDetails($imageDetails)
        );
    }

    protected function _imagePathFromImageDetails($imageDetails)
    {
        return  $imageDetails['path'] . $imageDetails['file'];
    }

    protected function _getCredentials()
    {
        $configuration = Mage::helper('cloudinary_cloudinary/configuration');

        $key = Key::fromString($configuration->getApiKey());
        $secret = Secret::fromString($configuration->getApiSecret());

        return new Credentials($key, $secret);
    }
}