<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{


    public function upload($imageDetails)
    {

        $cloudinary = new CloudinaryImageProvider();
        $cloudinary->upload(Image::fromPath($this->_imagePathFromImageDetails($imageDetails)), $this->getCredentials());

        return $cloudinary->wasUploadSuccessful();
    }

    protected  function getCredentials()
    {
        $configuration = Mage::helper('cloudinary_cloudinary/configuration');

        return new Credentials(
            Key::fromString($configuration->getApiKey()),
            Secret::fromString($configuration->getApiSecret())
        );
    }


    protected function _imagePathFromImageDetails($imageDetails)
    {
        return  preg_replace('/.tmp$/', '', $imageDetails['path'] . $imageDetails['file']);
    }
}